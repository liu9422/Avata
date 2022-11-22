<?php
namespace Avata\Client;

use Avata\Avata;

/**
 * Class Client
 * @package Avata\Client
 */
class Client
{
    protected $avata;

    protected $version = '/v1beta1';

    /**
     * Client constructor.
     * @param Avata $avata
     */
    public function __construct(Avata $avata)
    {
        $this->avata = $avata;
    }

    /**
     * @param string $operation_id
     * @return string
     */
    protected function getOperationId(string $operation_id = ''): string
    {
        if($operation_id === ''){
            $operation_id = strtoupper(md5(uniqid(md5(microtime(true)),true)));
        }
        return $operation_id;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getActionUrl(string $name): string
    {
        return $this->version . '/' . $name;
    }

    /**
     * @param string $field
     * @param $value
     * @param array $body
     * @param callable|null $inspect
     * @param callable|null $filter
     */
    protected function setBodyParam(
        string $field,
        $value,
        array &$body,
        callable $inspect = null,
        callable $filter = null
    )
    {
        if($field){
            if($inspect === null){
               if($value !== ''){
                   $body[$field] = $this->filter($value, $filter);
               }
            }else{
                if($inspect($value)){
                    $body[$field] = $this->filter($value, $filter);
                }
            }
        }
    }

    /**
     * @param $body
     * @param string $start_date
     * @param string $end_date
     * @param string $sort_by
     * @param string $offset
     * @param string $limit
     */
    public function setCommonBody(
        &$body,
        string $start_date,
        string $end_date,
        string $sort_by,
        string $offset,
        string $limit
    )
    {
        $this->setBodyParam('start_date', $start_date, $body, null, function ($value){
            return date('Y-m-d', strtotime($value));
        });
        $this->setBodyParam('end_date', $end_date, $body, null, function ($value){
            return date('Y-m-d', strtotime($value));
        });
        $this->setBodyParam('sort_by', $sort_by, $body);
        $this->setBodyParam('offset', $offset, $body, null, 'strval');
        $this->setBodyParam('limit', $limit, $body, null, 'strval');
    }

    /**
     * @param $value
     * @param callable|null $filter
     * @return mixed
     */
    private function filter($value, callable $filter = null)
    {
        if($filter !== null){
            return $filter($value);
        }
        return $value;
    }
}