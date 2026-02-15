<?php

namespace BcSeoAiAssist\Service;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Log\Log;

/**
 * OpenAiSeoService
 *
 */
class OpenAiSeoService
{
    /**
     * OpenAI API URL
     *
     * @var string
     */
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * SEOディスクリプションを生成
     *
     * @param string $body 本文
     * @param string $keywords キーワード
     * @param string $title
     * @param string $apiKey OpenAI APIキー
     * @return array 生成結果 ['success' => bool, 'description' => string, 'error' => string]
     */
    public function generateDescription(string $body, string $keywords = '', string $title = '', string $apiKey = ''): array
    {
        // APIキー確認
        if (empty($apiKey)) {
            return [
                'success' => false,
                'error' => 'APIキーが設定されていません。プラグイン設定でAPIキーを入力してください。'
            ];
        }

        // 本文チェック
        if (empty(trim($body))) {
            return [
                'success' => false,
                'error' => '本文が空のため生成できません。'
            ];
        }

        // 本文の前処理
        $processedBody = $this->preprocessBody($body);

        // キーワードをカンマ、スペース、改行で分割して取得
        $keywordArray = $this->parseKeywords($keywords);

        // プロンプト生成
        $prompt = $this->buildPrompt($processedBody, $keywordArray, $title);

        try {
            $response = $this->callOpenAiApi($prompt, $apiKey);

            if ($response['success']) {
                // 後処理
                $description = $this->postprocessDescription($response['content']);

                return [
                    'success' => true,
                    'description' => $description
                ];
            } else {
                return $response;
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'ディスクリプション生成中にエラーが発生しました。時間をおいて再度お試しください。'
            ];
        }
    }

    /**
     * 本文の前処理
     *
     * @param string $body 本文
     * @return string 処理後の本文
     */
    protected function preprocessBody(string $body): string
    {
        // HTMLタグを除去、空白、改行を整形
        $text = strip_tags($body);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        // 文字数制限
        $maxLength = Configure::read('BcSeoAiAssist.max_body_length', 3000);
        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength) . '...';
        }

        return $text;
    }

    /**
     * キーワードをパース
     *
     * @param string $keywords キーワード文字列
     * @return array キーワード配列
     */
    protected function parseKeywords(string $keywords): array
    {
        if (empty($keywords)) {
            return [];
        }

        $keywords = preg_split('/[,、\s\n\r]+/u', $keywords);
        $keywords = array_filter(array_map('trim', $keywords));

        return array_values($keywords);
    }

    /**
     * プロンプトを構築
     *
     * @param string $body 本文
     * @param array $keywords キーワード配列
     * @param string $title タイトル
     * @return string プロンプト
     */
    protected function buildPrompt(string $body, array $keywords, string $title): string
    {
        $maxLength = Configure::read('BcSeoAiAssist.max_description_length', 120);

        $prompt = "{$maxLength}文字程度のSEOディスクリプションを作成してください。\n\n";

        $prompt .= "【重要な要件】\n";
        $prompt .= "- 最初の40文字を特に魅力的で印象的な内容にすること\n";

        if (!empty($keywords)) {
            $keywordList = array_slice($keywords, 0, 3);
            $prompt .= "- キーワードを左から順に重要視して含めること（左が最も重要）: " . implode(', ', $keywordList) . "\n";
        }

        $prompt .= "\n";

        if (!empty($title)) {
            $prompt .= "タイトル: {$title}\n";
        }

        $prompt .= "本文:\n{$body}\n\n";
        $prompt .= "ディスクリプション({$maxLength}文字程度):";

        return $prompt;
    }

    /**
     * OpenAI APIを呼び出し
     *
     * @param string $prompt プロンプト
     * @param string $apiKey APIキー
     * @return array レスポンス
     */
    protected function callOpenAiApi(string $prompt, string $apiKey): array
    {
        $model = Configure::read('BcSeoAiAssist.model', 'gpt-4.1-nano');

        $requestData = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_completion_tokens' => 500
        ];

        $client = new Client(['timeout' => 30]);
        $response = $client->post($this->apiUrl, json_encode($requestData), [
            'type' => 'json',
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getJson();

        // 成功チェック
        if ($statusCode === 200 && !empty($body['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'content' => trim($body['choices'][0]['message']['content'])
            ];
        }

        // 失敗
        Log::error('OpenAI API error: Status ' . $statusCode);
        return [
            'success' => false,
            'error' => 'ディスクリプションの生成に失敗しました。'
        ];
    }

    /**
     * 生成結果の後処理
     *
     * @param string $description 生成されたディスクリプション
     * @return string 処理後のディスクリプション
     */
    protected function postprocessDescription(string $description): string
    {
        $description = trim($description);

        // 生成結果に含まれる場合がある引用符を除去
        $description = trim($description, '"\'「」『』');

        return $description;
    }
}
