<?php

use Cake\Routing\RouteBuilder;

/**
 * Routes
 */
return function (RouteBuilder $routes) {
    $routes->prefix('Admin', function (RouteBuilder $routes) {
        $routes->plugin('BcSeoAiAssist', ['path' => '/baser/admin/bc-seo-ai-assist'], function (RouteBuilder $routes) {
            $routes->connect('/settings', ['controller' => 'Settings', 'action' => 'index']);
            $routes->fallbacks();
        });
    });
};
