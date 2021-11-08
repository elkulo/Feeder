<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $log_file = isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app-' . date("Y-m-d") . '.log';
            return new Settings([

                /**
                 * サイト情報
                 */
                'site.title' => isset($_ENV['SITE_TITLE']) ? $_ENV['SITE_TITLE'] : 'Feeder',
                'site.description' => isset($_ENV['SITE_DESCRIPTION']) ? $_ENV['SITE_DESCRIPTION'] : '',
                'site.url' => isset($_ENV['SITE_URL'])? rtrim($_ENV['SITE_URL'], '/'): '/',
                'site.language' => isset($_ENV['SITE_LANGUAGE'])? $_ENV['SITE_LANGUAGE']: 'ja-JP',
                'site.category' => isset($_ENV['SITE_CATEGORY'])? $_ENV['SITE_CATEGORY']: 'Blog',
                'site.robots' => isset($_ENV['SITE_ROBOTS'])? $_ENV['SITE_ROBOTS']: 'noindex,follow',

                /**
                 * ユーティリティ
                 */
                'app.path' => dirname(__DIR__),
                'feeds.src' => __DIR__ . '/../../' . trim($_ENV['FEED_SOURCE'], '/'),
                'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                // Should be set to false in production.
                'displayErrorDetails' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                'logError'            => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'false' : true,
                'logErrorDetails'     => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'false' : true,
                'logger' => [
                    'name' => 'feeder',
                    'path' => $log_file,
                    'level' => Logger::DEBUG,
                ],
                'twig' => [
                    'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                    'auto_reload' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                    'strict_variables' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                    'cache' => __DIR__ . '/../var/cache/twig',
                ],
            ]);
        }
    ]);
};
