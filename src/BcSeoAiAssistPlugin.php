<?php

namespace BcSeoAiAssist;

use BaserCore\BcPlugin;
use BaserCore\Error\BcException;
use Cake\Core\Plugin;

/**
 * BcSeoAiAssistPlugin
 */
class BcSeoAiAssistPlugin extends BcPlugin
{
    /**
     * プラグインをインストールする
     *
     * @param array $options
     * @return bool
     * @throws BcException
     */
    public function install($options = []): bool
    {
        // BcSeoプラグインの依存関係チェック
        if (!Plugin::isLoaded('BcSeo')) {
            throw new BcException(__d('baser_core', 'BcSeoAiAssist プラグインを利用するには、BcSeo プラグインを有効化する必要があります。'));
        }

        return parent::install($options);
    }
}
