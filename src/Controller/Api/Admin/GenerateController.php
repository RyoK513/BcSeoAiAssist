<?php

namespace BcSeoAiAssist\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcSeoAiAssist\Service\OpenAiSeoService;
use BaserCore\Service\SiteConfigsServiceInterface;

/**
 * GenerateController
 *
 * SEOディスクリプション生成API
 */
class GenerateController extends BcAdminApiController
{
    /**
     * SEOディスクリプションを生成
     *
     * @param SiteConfigsServiceInterface $siteConfigService
     * @return void
     */
    public function generate(SiteConfigsServiceInterface $siteConfigService)
    {
        $this->request->allowMethod(['post']);

        // リクエストデータ取得
        $body = $this->request->getData('body', '');
        $keywords = $this->request->getData('keywords', '');
        $title = $this->request->getData('content_title', '');

        $siteConfig = $siteConfigService->get();
        $apiKey = $siteConfig->bc_seo_ai_assist_api_key ?? '';

        // ディスクリプションを生成
        $service = new OpenAiSeoService();
        $result = $service->generateDescription($body, $keywords, $title, $apiKey);

        if ($result['success']) {
            $this->set([
                'success' => true,
                'description' => $result['description'],
                'message' => 'SEOディスクリプションを生成しました。'
            ]);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $this->set([
                'success' => false,
                'error' => $result['error'],
                'message' => $result['error']
            ]);
        }

        $this->viewBuilder()->setOption('serialize', ['success', 'description', 'error', 'message']);
    }
}
