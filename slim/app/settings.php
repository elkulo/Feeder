<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Dotenv\Dotenv;

return function (ContainerBuilder $containerBuilder) {

    // Dotenv
    $env = __DIR__ . '/../.env';
    try {
        if (is_readable($env)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        } else {
            throw new Exception('環境設定ファイルがありません');
        }
    } catch (Exception $e) {
        exit($e->getMessage());
    }

    // Timezone
    if (isset($_ENV['TIME_ZONE'])) {
        date_default_timezone_set($_ENV['TIME_ZONE']);
    }

    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [

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
            'feed.src' => __DIR__ . '/../' . $_ENV['FEED_SOURCE'],
            'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] : false,
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app-' . date("Y-m-d") . '.log',
                'level' => Logger::DEBUG,
            ],
            'twig' => [
                'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] : false,
                'strict_variables' => true,
                'cache' => __DIR__ . '/../var/cache/twig',
            ],
        ],
    ]);
};
