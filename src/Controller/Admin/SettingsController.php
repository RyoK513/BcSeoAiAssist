<?php

namespace BcSeoAiAssist\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Service\SiteConfigsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * SettingsController
 *
 * プラグイン設定画面
 */
class SettingsController extends BcAdminAppController
{
    /**
     * 設定画面
     *
     * @param SiteConfigsServiceInterface $siteConfigService
     * @return void
     */
    public function index(SiteConfigsServiceInterface $siteConfigService)
    {
        // サイト基本設定取得
        $siteConfig = $siteConfigService->get();

        if ($this->request->is(['post', 'put', 'patch'])) {
            try {
                $apiKey = $this->request->getData('bc_seo_ai_assist_api_key', '');

                if (empty($apiKey)) {
                    $this->BcMessage->setError('APIキーを入力してください。');
                } else {
                    $data = ['bc_seo_ai_assist_api_key' => $apiKey];
                    $siteConfigService->update($data);

                    $this->BcMessage->setSuccess('設定を保存しました。');
                    return $this->redirect(['action' => 'index']);
                }
            } catch (PersistenceFailedException $e) {
                $this->BcMessage->setError('設定の保存に失敗しました。');
            } catch (\Throwable $e) {
                $this->BcMessage->setError('設定の保存中にエラーが発生しました。' . $e->getMessage());
            }
        }

        // 入力欄に値を表示
        $siteConfig = $siteConfigService->get();

        $this->set('siteConfig', $siteConfig);
    }
}
