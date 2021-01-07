<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
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
        $twig = $app->getContainer()->get(Twig::class);
        return $twig->render($response, 'home.twig', [
            // 'title' => '',
            // 'description' => '',
        ]);
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/posts', function (Group $group) {
        $group->get('', ListPostsAction::class);
        $group->get('/{id}', ViewPostAction::class);
    });
};
