<?php
declare(strict_types=1);

namespace App\Application\Actions\Post;

use Psr\Http\Message\ResponseInterface as Response;
use FeedWriter\RSS2;

class RSSPostsAction extends PostAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->logger->info("RSS Feed was viewed.");

        // チャンネル情報の登録
        $feed = new RSS2;
        $feed->setTitle('Feeder | el.kulo');
        $feed->setLink('http://localhost:8000/');
        $feed->setDate(new \DateTime());
        $feed->setDescription('ホームページの説明');
        $feed->setChannelElement('language', 'ja-JP');
        $feed->setChannelElement('pubDate', date(\DATE_RSS, time()));
        $feed->setChannelElement('category', 'Blog');

        // 全フィードを取得
        $sites = $this->postRepository->findAll();

        // フィードのアイテムを追加
        // TODO: 新着順に並べ替え
        foreach ($sites as $site) {
            foreach ($site->feeder as $data) {
                $item = $feed->createNewItem();
                $item->setDate($data['date']);
                $item->setTitle($data['title']);
                $item->setLink($data['link']);
                $item->setDescription($data['content']);
                $item->setId($data['link'], true);
                $item->setAuthor($site->name);
                $feed->addItem($item);
            }
        }

        $xml = $feed->generateFeed();

        // headeを上書き
        $response = $this->response->withHeader('Content-Type', 'application/xml');

        // bodyを生成
        $response->getBody()->write($xml);
        
        return $response;
    }
}
