<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Application\Settings\SettingsInterface;
use App\Domain\Post\Post;
use App\Domain\Post\PostNotFoundException;
use App\Domain\Post\PostRepository;
use FeedWriter\RSS2;

class InMemoryPostRepository implements PostRepository
{

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var Post[]
     */
    private $posts;

    /**
     * InMemoryPostRepository constructor.
     *
     * @param array|null $posts
     * @param SettingsInterface $settings
     */
    public function __construct(array $posts = null, SettingsInterface $settings)
    {
        $this->settings = $settings;
        $appPath = $settings->get('app.path');
        $src = $settings->get('feeds.src');
        $resource = [];
        $posts = [];
        $id = 0;

        if (is_readable($src)) {
            $json = file_get_contents($src);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $resource = json_decode($json, true);
        }

        $sites = $resource ? $resource :
            [
                [
                    'id' => 1,
                    'name' => 'Yahoo主要',
                    'src' => 'https://news.yahoo.co.jp/rss/topics/top-picks.xml',
                    'url' => 'https://news.yahoo.co.jp/',
                    'category' => ['Yahoo']
                ]
            ];

        foreach ($sites as $site) {
            $id++;

            // @reference https://simplepie.org/wiki/reference/simplepie/start
            $rss = new \SimplePie;

            // フィードを指定.
            $rss->set_feed_url($site['src']);

            // Cache ディレクトリを指定.
            $rss->set_cache_location($appPath . '/var/cache/simplepie');
            $success = $rss->init();

            // 投稿格納
            $entries = [];

            // RSSが取得できたら情報をパース
            if ($success) {
                // 表示件数を指定.
                $rssItems = $rss->get_items(0, 50);
                foreach ($rssItems as $item) {
                    $entries[] = [
                        'date' => $item->get_date('Y/m/d H:i:s'),
                        'title' => $item->get_title(),
                        'link' =>  $item->get_link(),
                        'content' => $item->get_description()? strip_tags($item->get_description()) :'',
                    ];
                }

                $posts[] = new Post(
                    $id,
                    $site['name'],
                    $site['src'],
                    $site['url'],
                    $site['category'],
                    $entries
                );
            }
        }

        $this->posts = $posts ?? [
            1 => new Post(1, '404 Not Found', '', '', [], [])
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

    /**
     * {@inheritdoc}
     */
    public function findRSS(): string
    {
        $settings = $this->settings;
        $feedAllArray = [];
        $updated = [];

        // チャンネル情報の登録
        $feed = new RSS2;
        $feed->setTitle($settings->get('site.title'));
        $feed->setDescription($settings->get('site.description'));
        $feed->setLink($settings->get('site.url'));
        $feed->setDate(new \DateTime());
        $feed->setChannelElement('pubDate', date(\DATE_RSS, time()));
        $feed->setChannelElement('language', $settings->get('site.language'));
        $feed->setChannelElement('category', $settings->get('site.category'));

        // 全フィードを取得
        $sites = $this->posts;

        // フィードをフラットに生成
        foreach ($sites as $site) {
            $siteFeeder = $site->getFeeder();
            foreach ($siteFeeder as $data) {
                $data['author'] = $site->getName();
                $feedAllArray[] = $data;
            }
        }

        // 日付でソート
        foreach ($feedAllArray as $key => $value) {
            $updated[$key] = $value['date'];
        }
        array_multisort($updated, SORT_DESC, $feedAllArray);

        // フィードのアイテムを追加
        foreach ($feedAllArray as $data) {
            $item = $feed->createNewItem();
            $item->setDate($data['date']);
            $item->setTitle($data['title']);
            $item->setLink($data['link']);
            $item->setDescription($data['content']);
            $item->setId($data['link'], true);
            $item->setAuthor($data['author']);
            $feed->addItem($item);
        }

        // xmlを返り値
        return $feed->generateFeed();
    }
}
