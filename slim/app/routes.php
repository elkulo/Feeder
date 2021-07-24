<?php
declare(strict_types=1);

use App\Application\Actions\Post\ListPostsAction;
use App\Application\Actions\Post\ViewPostAction;
use App\Application\Actions\Post\RSSPostsAction;
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

    // HOME
    $app->get('/', function (Request $request, Response $response) use ($app) {
        $twig = $app->getContainer()->get(Twig::class);
        $settings = $app->getContainer()->get('settings');
        return $twig->render($response, 'home.twig', [
            'title' => $settings['site.title'],
            'description' => $settings['site.description'],
            'robots' => $settings['site.robots']
        ]);
    });

    // API
    $app->group('/api/v1/posts', function (Group $group) {
        $group->get('', ListPostsAction::class);
        $group->get('/{id}', ViewPostAction::class);
    });

    // RSS Output
    $app->get('/rss', RSSPostsAction::class);
};
