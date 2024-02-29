<?php

namespace App\Entities;

abstract class Storage
{
    abstract function create(TelegraphText $text): string;

    abstract function read(string $slug): ?TelegraphText;

    abstract function update(TelegraphText $content, string $slug): void;

    abstract function delete(string $slug): void;

    abstract function list(): array;
}