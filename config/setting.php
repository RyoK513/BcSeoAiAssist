<?php
return [
    'BcSeoAiAssist' => [
        // 使用するモデル
        'model' => 'gpt-4.1-nano',
        // ディスクリプション目安文字数
        'max_description_length' => 120,
        // 冒頭PR強調文字数
        'pr_emphasis_length' => 40,
        // 本文の最大処理文字数
        'max_body_length' => 3000,
    ]
];
