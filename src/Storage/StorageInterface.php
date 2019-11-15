<?php 

namespace Jakmall\Recruitment\Calculator\Storage;

interface StorageInterface
{
    public function insert(Array $data): void;

    public function clear(): void;

    public function get($filter): array;
}