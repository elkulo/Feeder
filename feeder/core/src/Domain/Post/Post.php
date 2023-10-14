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
    private $src;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $category;

    /**
     * @var array
     */
    private $feeder;

    /**
     * @param int|null  $id
     * @param string    $name
     * @param string    $src
     * @param string    $url
     * @param array     $category
     * @param array     $entries
     */
    public function __construct(?int $id, string $name, string $src, string $url, array $category, array $entries)
    {
        $this->id = $id;
        $this->name = $name;
        $this->src = $src;
        $this->url = $url;
        $this->category = $category;
        $this->feeder = $entries;
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
     * @return mixed
     */
    public function jsonSerialize(): mixed
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
