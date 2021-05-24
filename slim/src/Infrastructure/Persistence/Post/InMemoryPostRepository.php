<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Domain\Post\Post;
use App\Domain\Post\PostNotFoundException;
use App\Domain\Post\PostRepository;

class InMemoryPostRepository implements PostRepository
{
    /**
     * @var Post[]
     */
    private $posts;

    /**
     * InMemoryPostRepository constructor.
     *
     * @param array|null $posts
     */
    public function __construct(array $posts = null)
    {
        if (is_readable(FEED_DB)) {
            $json = file_get_contents(FEED_DB);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $data = json_decode($json, true);
            for ($i=1; $i <= count($data); $i++) {
                if (isset($data[$i - 1])) {
                    $posts[$i] = new Post(
                        $i,
                        $data[$i - 1]['name'],
                        $data[$i - 1]['feed'],
                        $data[$i - 1]['url'],
                        $data[$i - 1]['category']
                    );
                }
            }
        }

        $this->posts = $posts ?? [
            1 => new Post(1, 'name', 'feed', 'url', []),
            2 => new Post(2, 'name', 'feed', 'url', []),
            3 => new Post(3, 'name', 'feed', 'url', []),
            4 => new Post(4, 'name', 'feed', 'url', []),
            5 => new Post(5, 'name', 'feed', 'url', [])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->posts);
    }

    /**
     * {@inheritdoc}
     */
    public function findPostOfId(int $id): Post
    {
        if (!isset($this->posts[$id])) {
            throw new PostNotFoundException();
        }

        return $this->posts[$id];
    }
}
