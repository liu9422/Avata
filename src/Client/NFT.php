<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

class NFT extends Client
{
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    public function createNFTCategory(
        string $name,
        string $owner,
        string $operation_id,
        string $class_id = '',
        string $symbol = '',
        string $description = '',
        string $uri = '',
        string $uri_hash = '',
        string $data = '',
        string $tag = ''
    )
    {
        $body['name'] = $name;
        $body['owner'] = $owner;
        $body['operation_id'] = $this->getOperationId($operation_id);

        if($class_id !== ''){
            $body['class_id'] = $class_id;
        }

        return $this->avata->request($this->getActionUrl('accounts'), $body, 'POST');
    }
}