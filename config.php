<?php
return [
    'type' => 'Plugin',
    'title' => __d('baser_core', 'SEO AI アシスト'),
    'description' => __d('baser_core', 'OpenAI APIを利用してSEOディスクリプションを自動生成するプラグインです。'),
    'author' => 'Ryo',
    'url' => 'https://ryoblog.net',
    'adminLink' => ['plugin' => 'BcSeoAiAssist', 'controller' => 'Settings', 'action' => 'index'],
    'installMessage' => __d('baser_core', 'プラグイン設定でOpenAI APIキーを設定してください。BcSeoプラグインが必要です。')
];
