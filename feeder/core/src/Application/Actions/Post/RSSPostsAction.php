<?php
declare(strict_types=1);

namespace App\Application\Actions\Post;

use Psr\Http\Message\ResponseInterface as Response;

class RSSPostsAction extends PostAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {

        $this->logger->info('RSS Feed was viewed.');

        $xml = $this->postRepository->findRSS();

        // headeを上書き
        $response = $this->response->withHeader('Content-Type', 'application/xml');

        // bodyを生成
        $response->getBody()->write($xml);

        return $response;
    }
}
