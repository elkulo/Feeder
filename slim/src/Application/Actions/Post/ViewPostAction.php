<?php
declare(strict_types=1);

namespace App\Application\Actions\Post;

use Psr\Http\Message\ResponseInterface as Response;

class ViewPostAction extends PostAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $postId = (int) $this->resolveArg('id');
        $post = $this->postRepository->findPostOfId($postId);

        $this->logger->info("Post of id `${postId}` was viewed.");

        return $this->respondWithData($post);
    }
}
