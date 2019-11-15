<?php 

namespace Jakmall\Recruitment\Calculator\Storage\JSON;

use Jakmall\Recruitment\Calculator\Storage\StorageInterface;
use Jajo\JSONDB;

class JSONStorage implements StorageInterface
{
    protected $fileName = 'history.json';

    public function __construct()
    {
        $this->storage = new JSONDB( __DIR__ . DIRECTORY_SEPARATOR . 'File');
    }

    public function insert(Array $data): void
    {
        $this->storage->insert( $this->fileName, 
            [ 
                'name' => 'Thomas', 
                'state' => 'Nigeria', 
                'age' => 22 
            ]
        );
    }

    public function clear(): void
    {
    }

    public function get($filter): array
    {
        $historical = $this
            ->storage
            ->from($this->fileName)
            ->get();

        return array_map(function($data){
            return [$data->name, $data->age, $data->state];
        }, $historical);
    }


}