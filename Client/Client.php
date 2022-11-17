<?php
namespace Avata\Client;

use Avata\Avata;

class Client
{
    protected $avata;

    public function __construct(Avata $avata)
    {
        $this->avata = $avata;
    }

    protected function getOperationId($operation_id = '')
    {
        if($operation_id === ''){
            $operation_id = strtoupper(md5(uniqid(md5(microtime(true)),true)));
        }
        return $operation_id;
    }
}