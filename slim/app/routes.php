<?php

declare(strict_types=1);

use App\Application\Actions\Post\ListPostsAction;
use App\Application\Actions\Post\ViewPostAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($app) {

        $feeder = array();
        $sites = [
            [
                'name' => 'No.1',
                'feed' => 'https://elkulo.github.io/rss.xml',
            ],
            [
                'name' => 'No.2',
                'feed' => 'https://elkulo.github.io/rss.xml',
            ]
        ];

        foreach ($sites as $site) {
            // @reference https://simplepie.org/wiki/reference/simplepie/start
            $rss = new SimplePie;

            // フィードを指定.
            $rss->set_feed_url($site['feed']);

            // Cache ディレクトリを指定.
            $rss->set_cache_location(__DIR__ . '/../var/cache/simplepie');
            $success = $rss->init();

            // 投稿格納
            $posts = array();

            // RSSが取得できない時はエラー出力
            $succeed = false;

            // RSSが取得できたら情報をパース
            if ($success) {
                // 表示件数を指定.
                $rssItems = $rss->get_items(0, 20);
                foreach ($rssItems as $item) {
                    $posts[] = [
                        'date' => $item->get_date('Y/m/d'),
                        'title' => $item->get_title(),
                        'link' =>  $item->get_link(),
                        'content' => $item->get_description(),
                    ];
                }
                $succeed = true;
            } else {
                // RSSが取得できない時はエラー出力
                $succeed = false;
            }

            $feeder[] = array(
                'name' => $site['name'],
                'posts' => $posts,
                'succeed' => $succeed
            );
        }

        $twig = $app->getContainer()->get(Twig::class);
        return $twig->render($response, 'home.twig', [
            'title' => '',
            'description' => '',
            'feeder' => $feeder
        ]);
    });

    /*
    $app->group('/posts', function (Group $group) {
        $group->get('', ListPostsAction::class);
        $group->get('/{id}', ViewPostAction::class);
    });
    */
};
