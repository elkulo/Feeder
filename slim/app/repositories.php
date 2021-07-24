<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use App\Domain\Post\PostRepository;
use App\Infrastructure\Persistence\Post\InMemoryPostRepository;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our PostRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        PostRepository::class => \DI\autowire(InMemoryPostRepository::class),
    ]);
};
