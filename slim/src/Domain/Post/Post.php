<?php

declare(strict_types=1);

namespace App\Domain\Post;

use JsonSerializable;

class Post implements JsonSerializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $feed;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $category;

    /**
     * @param int|null  $id
     * @param string    $name
     * @param string    $feed
     * @param string    $url
     * @param array     $category
     */
    public function __construct(?int $id, string $name, string $feed, string $url, array $category)
    {
        $this->id = $id;
        $this->name = $name;
        $this->feed = $feed;
        $this->url = $url;
        $this->category = $category;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFeed(): string
    {
        return $this->feed;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getCategory(): array
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'feed' => $this->feed,
            'url' => $this->url,
            'category' => $this->category,
        ];
    }
}
