<?php
namespace Avata\Client;

use Avata\Avata;

class Account extends Client
{
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    public function createChainAccount($name, $operation_id = '')
    {
        $body['name'] = $name;
        return $this->avata->request('/account', );
    }
}