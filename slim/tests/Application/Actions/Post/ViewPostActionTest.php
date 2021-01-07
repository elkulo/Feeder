<?php
declare(strict_types=1);

namespace Tests\Application\Actions\Post;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Post\Post;
use App\Domain\Post\PostNotFoundException;
use App\Domain\Post\PostRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewPostActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $post = new Post(1, []);

        $postRepositoryProphecy = $this->prophesize(PostRepository::class);
        $postRepositoryProphecy
            ->findPostOfId(1)
            ->willReturn($post)
            ->shouldBeCalledOnce();

        $container->set(PostRepository::class, $postRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/posts/1');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $post);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsPostNotFoundException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();

        $postRepositoryProphecy = $this->prophesize(PostRepository::class);
        $postRepositoryProphecy
            ->findPostOfId(1)
            ->willThrow(new PostNotFoundException())
            ->shouldBeCalledOnce();

        $container->set(PostRepository::class, $postRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/posts/1');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The post you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
