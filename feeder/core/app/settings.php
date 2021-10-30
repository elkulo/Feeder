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
                'site.URL' => $_ENV['SITE_URL'],
                'site.language' => $_ENV['SITE_LANGUAGE'],
                'site.category' => $_ENV['SITE_CATEGORY'],
                'site.robots' => $_ENV['SITE_ROBOTS'],

                /**
                 * ユーティリティ
                 */
                'slim.path' => dirname(__DIR__),
                'feed.src' => __DIR__ . '/../../' . ltrim($_ENV['FEED_SOURCE'], '/'),
                'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                // Should be set to false in production.
                'displayErrorDetails' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'true' : false,
                'logError'            => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'false' : true,
                'logErrorDetails'     => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] === 'false' : true,
                'logger' => [
                    'name' => 'slim-app',
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