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
        if (is_readable(STORAGE_JSON)) {
            $json = file_get_contents(STORAGE_JSON);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $data = json_decode($json, true);
            for ($i=1; $i <= count($data); $i++) {
                if (isset($data[$i - 1])) {
                    $posts[$i] = new Post($i, $data[$i - 1]);
                }
            }
        }

        $this->posts = $posts ?? [
            1 => new Post(1, []),
            2 => new Post(2, []),
            3 => new Post(3, []),
            4 => new Post(4, []),
            5 => new Post(5, []),
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
