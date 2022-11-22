<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

/**
 * 交易结果查询接口
 * Class Account
 * @package Avata\Client
 */
class Transaction extends Client
{
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    /**
     * 上链交易结果查询
     * 根据在接口请求时自定义的 Operation ID ，查询相关的链上操作结果。每笔交易会产生唯一的 Operation ID，根据 Operation ID，
     * 可以查询具体的交易结果，包含交易状态、交易信息及交易详情。 Operation ID 的值为原 Task ID 对应的值，建议程序中尽早将 Task ID 替换为 Operation ID。
     * @param $operation_id string 操作 ID，是指用户在进行具体的NFT/MT/业务接口请求时，自定义的操作ID
     * @return Response
     * @throws AvataException
     */
    public function queryChainTransactionResult(string $operation_id): Response
    {
        return $this->avata->request($this->getActionUrl('tx/' . $operation_id));
    }

    /**
     * 上链交易排队状态查询
     * 应用平台方可调用此接口查看 Avata 平台的当前链交易排队情况以及待处理的交易数量，辅助业务上链时间的选择决策；
     * 也可以指定 Operation ID 来查询对应交易的排队状态
     * @param string $operation_id 操作 ID，是指用户在进行具体的NFT/MT/业务接口请求时，自定义的操作ID
     * @return Response
     * @throws AvataException
     */
    public function queryChainTransactionStatus(string $operation_id): Response
    {
        $body['operation_id'] = $operation_id;
        return $this->avata->request($this->getActionUrl('tx/queue/info'), $body);
    }
}