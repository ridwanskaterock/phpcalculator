<?php 

namespace Jakmall\Recruitment\Calculator\Storage;

class StorageService implements StorageInterface
{
    /**
     * @var string
     */
    protected $vendor;

    public function __construct($storage)
    {
        $this->vendor = $storage;
    }

    public function insert(Array $data): void 
    {
        $this->vendor->insert($data);
    }

    public function clear(): void
    {
        $this->vendor->clear();
    }

    public function get($filter): array
    {
        return $this->vendor->get($filter);
    }


}