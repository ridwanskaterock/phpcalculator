<?php 

namespace Jakmall\Recruitment\Calculator\Storage\JSON;

use Jakmall\Recruitment\Calculator\Storage\StorageInterface;
use Jajo\JSONDB;

class JSONStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected $fileName = 'history.json';

    /**
     * @var integer
     */
    protected $iteration = 1;

    public function __construct()
    {
        $this->storage = new JSONDB( __DIR__ . DIRECTORY_SEPARATOR . 'File');
    }

    public function insert(Array $data): void
    {
        $this->storage->insert( $this->fileName, $data);
    }

    public function clear(): void
    {
        $this
            ->storage
            ->from($this->fileName)
            ->delete()
        ;
    }

    public function get($filter): array
    {
        $historical = $this
            ->storage
            ->from($this->fileName)
            ->get()
        ;

        $historical = array_filter(
            $historical,
            function($data) use($filter) {
                $data = (object) $data;
                if (count($filter) == 0) {
                    return true;
                }
                return in_array($data->command, $filter);
            }
        );

        return array_map(function($data){
            $data = (object) $data;
            return [
                $this->iteration++,
                ucfirst($data->command), 
                $data->description, 
                $data->result,
                $data->output,
                $data->time,
            ];
        }, $historical);
    }

}