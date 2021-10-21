<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Post;

use App\Domain\Post\Post;
use App\Domain\Post\PostNotFoundException;
use App\Infrastructure\Persistence\Post\InMemoryPostRepository;
use Tests\TestCase;

class InMemoryPostRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $post = new Post(1, []);

        $postRepository = new InMemoryPostRepository([1 => $post]);

        $this->assertEquals([$post], $postRepository->findAll());
    }

    public function testFindAllPostsByDefault()
    {
        $posts = [
            1 => new Post(1, []),
            2 => new Post(2, []),
            3 => new Post(3, []),
            4 => new Post(4, []),
            5 => new Post(5, []),
        ];

        $postRepository = new InMemoryPostRepository();

        $this->assertEquals(array_values($posts), $postRepository->findAll());
    }

    public function testFindPostOfId()
    {
        $post = new Post(1, []);

        $postRepository = new InMemoryPostRepository([1 => $post]);

        $this->assertEquals($post, $postRepository->findPostOfId(1));
    }

    public function testFindPostOfIdThrowsNotFoundException()
    {
        $postRepository = new InMemoryPostRepository([]);
        $this->expectException(PostNotFoundException::class);
        $postRepository->findPostOfId(1);
    }
}
