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

        if (is_readable(FEEDS_DB)) {
            $json = file_get_contents(FEEDS_DB);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $feeds = json_decode($json, true);
        }

        $sites = $feeds ? $feeds :
            [
                [
                    'id' => '1',
                    'name' => '主要',
                    'feed' => 'https://news.yahoo.co.jp/rss/topics/top-picks.xml',
                ],
                [
                    'id' => '2',
                    'name' => 'エンタメ',
                    'feed' => 'https://news.yahoo.co.jp/rss/topics/entertainment.xml',
                ],
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
                        'date' => $item->get_date('Y/m/d H:i:s'),
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
                'id' => $site['id'],
                'name' => $site['name'],
                'posts' => $posts,
                'succeed' => $succeed
            );
        }

        $twig = $app->getContainer()->get(Twig::class);
        return $twig->render($response, 'home.twig', [
            'title' => 'Feeder',
            'description' => '',
            'feeder' => $feeder
        ]);
    });

    /*
    $app->group('/feeds', function (Group $group) {
        $group->get('', ListPostsAction::class);
        $group->get('/{id}', ViewPostAction::class);
    });
    */
};
