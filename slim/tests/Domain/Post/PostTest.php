<?php
declare(strict_types=1);

namespace Tests\Domain\Post;

use App\Domain\Post\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function postProvider()
    {
        return [
            [1, []],
            [2, []],
            [3, []],
            [4, []],
            [5, []],
        ];
    }

    /**
     * @dataProvider postProvider
     * @param $id
     * @param $data
     */
    public function testGetters($id, $data)
    {
        $post = new Post($id, $data);

        $this->assertEquals($id, $post->getId());
        $this->assertEquals($data, $post->getData());
    }

    /**
     * @dataProvider postProvider
     * @param $id
     * @param $data
     */
    public function testJsonSerialize($id, $data)
    {
        $post = new Post($id, $data);

        $expectedPayload = json_encode([
            'id' => $id,
            'data' => $data,
        ]);

        $this->assertEquals($expectedPayload, json_encode($post));
    }
}
