<?php
declare(strict_types=1);

namespace Tests\Application\Actions\Post;

use App\Application\Actions\ActionPayload;
use App\Domain\Post\PostRepository;
use App\Domain\Post\Post;
use DI\Container;
use Tests\TestCase;

class ListPostActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $post = new Post(1, []);

        $postRepositoryProphecy = $this->prophesize(PostRepository::class);
        $postRepositoryProphecy
            ->findAll()
            ->willReturn([$post])
            ->shouldBeCalledOnce();

        $container->set(PostRepository::class, $postRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/posts');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$post]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
