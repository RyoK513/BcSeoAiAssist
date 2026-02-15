<?php

namespace BcSeoAiAssist\Event;

use Cake\Event\EventListenerInterface;
use Cake\Event\EventInterface;

/**
 * BcSeoAiAssistViewEventListener
 *
 */
class BcSeoAiAssistViewEventListener implements EventListenerInterface
{
    /**
     * 実行済みフラグ(重複実行防止)
     *
     * @var bool
     */
    private static $executed = false;

    /**
     * イベント実装
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'View.beforeLayout' => 'beforeLayout'
        ];
    }

    /**
     * レイアウト描画前のイベント
     *
     * SEOフォームの後にボタンを追加
     *
     * @param EventInterface $event
     * @return void
     */
    public function beforeLayout(EventInterface $event)
    {
        // 既に実行済みならスキップ
        if (self::$executed) {
            return;
        }

        $view = $event->getSubject();

        // SEOフォームが含まれているかチェック
        $content = $view->fetch('content');
        if (strpos($content, 'id="SeoSettingBody"') !== false) {
            $button = $view->element('BcSeoAiAssist.generate_button');

            // id="SeoSettingBody"の閉じタグ</div>の直前にボタンを挿入
            $pattern = '/(id="SeoSettingBody"[^>]*>)(.*?)(<\/div>)/s';
            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[3][1];
                $content = substr_replace($content, $button, $pos, 0);
                $view->assign('content', $content);

                // 重複防止フラグを立てる
                self::$executed = true;
            }
        }
    }
}
