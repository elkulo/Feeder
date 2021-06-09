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
        $resource = array();

        if (is_readable(FEED_DB)) {
            $json = file_get_contents(FEED_DB);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $resource = json_decode($json, true);
        }

        $sites = $resource ? $resource :
            [
                [
                    "id" => "1",
                    "name" => "Yahoo主要",
                    "src" => "https://news.yahoo.co.jp/rss/topics/top-picks.xml",
                    "url" => "https://news.yahoo.co.jp/",
                    "category" => ["Yahoo"]
                ]
            ];

        foreach ($sites as $site) {
            // @reference https://simplepie.org/wiki/reference/simplepie/start
            $rss = new \SimplePie;

            // フィードを指定.
            $rss->set_feed_url($site['src']);

            // Cache ディレクトリを指定.
            $rss->set_cache_location(SLIM_PATH . '/var/cache/simplepie');
            $success = $rss->init();

            // 投稿格納
            $feeder = array();

            // RSSが取得できたら情報をパース
            if ($success) {
                // 表示件数を指定.
                $rssItems = $rss->get_items(0, 20);
                foreach ($rssItems as $item) {
                    $feeder[] = [
                        'date' => $item->get_date('Y/m/d H:i:s'),
                        'title' => $item->get_title(),
                        'link' =>  $item->get_link(),
                        'content' => $item->get_description(),
                    ];
                }

                $posts[] = new Post(
                    (int) $site['id'],
                    $site['name'],
                    $site['src'],
                    $site['url'],
                    $site['category'],
                    $feeder
                );
            }
        }

        $this->posts = $posts ?? [
            1 => new Post(1, 'Not Found', '/', '/', [], [])
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
        if (!isset($this->posts[$id - 1])) {
            throw new PostNotFoundException();
        }

        return $this->posts[$id - 1];
    }
}
