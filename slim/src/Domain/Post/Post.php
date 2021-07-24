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
    public $name;

    /**
     * @var string
     */
    public $src;

    /**
     * @var string
     */
    public $url;

    /**
     * @var array
     */
    public $category;

    /**
     * @var array
     */
    public $feeder;

    /**
     * @param int|null  $id
     * @param string    $name
     * @param string    $src
     * @param string    $url
     * @param array     $category
     * @param array     $feeder
     */
    public function __construct(?int $id, string $name, string $src, string $url, array $category, array $feeder)
    {
        $this->id = $id;
        $this->name = $name;
        $this->src = $src;
        $this->url = $url;
        $this->category = $category;
        $this->feeder = $feeder;
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
    public function getSrc(): string
    {
        return $this->src;
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
    public function getFeeder(): array
    {
        return $this->feeder;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'src' => $this->src,
            'url' => $this->url,
            'category' => $this->category,
            'feeder' => $this->feeder
        ];
    }
}
