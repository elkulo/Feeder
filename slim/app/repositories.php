<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use App\Domain\Post\PostRepository;
use App\Infrastructure\Persistence\Post\InMemoryPostRepository;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);

    // Here we map our PostRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        PostRepository::class => \DI\autowire(InMemoryPostRepository::class),
    ]);
};
