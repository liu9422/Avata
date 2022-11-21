<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

/**
 * 链账户接口
 * Class Account
 * @package Avata\Client
 */
class Account extends Client
{
    /**
     * Account constructor.
     * @param Avata $avata
     */
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    /**
     * 创建链账户
     * @param string $name 链账户名称
     * @param string $operation_id 操作 ID，保证幂等性
     * @return Response
     * @throws AvataException
     */
    public function createChainAccount(string $name, string $operation_id): Response
    {
        $body['name'] = $name;
        $body['operation_id'] = $this->getOperationId($operation_id);
        return $this->avata->request($this->getActionUrl('account'), $body, 'POST');
    }

    /**
     * 批量创建链账户
     * @param int $count 批量创建链账户的数量
     * @param string $operation_id 操作 ID，保证幂等性
     * @return Response
     * @throws AvataException
     */
    public function batchCreateChainAccount(int $count, string $operation_id): Response
    {
        $body['count'] = $count;
        $body['operation_id'] = $this->getOperationId($operation_id);
        return $this->avata->request($this->getActionUrl('accounts'), $body, 'POST');
    }

    /**
     * 查询链账户
     * @param string $account 链账户地址
     * @param string $name 链账户名称，支持模糊查询
     * @param string $operation_id 操作 ID。此操作 ID 需要填写在请求创建链账户/批量创建链账户接口时，返回的 Operation ID
     * @param string $start_date 日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_data 日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param int $offset 游标，默认为 0
     * @param int $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryChainAccount(
        string $account = '',
        string $name = '',
        string $operation_id = '',
        string $start_date = '',
        string $end_data = '',
        string $sort_by = 'DATE_ASC',
        $offset = 0,
        $limit = 10
    ): Response
    {
        $body = [];
        if($account !== ''){
            $body['account'] = $account;
        }

        if($name !== ''){
            $body['name'] = $name;
        }

        if($operation_id !== ''){
            $body['operation_id'] = $operation_id;
        }

        if($start_date !== ''){
            $body['start_date'] = date('Y-m-d', strtotime($start_date));
        }

        if($end_data !== ''){
            $body['end_data'] = date('Y-m-d', strtotime($end_data));
        }

        if($sort_by !== ''){
            $body['sort_by'] = $sort_by;
        }

        if($offset){
            $body['offset'] = strval($offset);
        }

        if($limit){
            $body['limit'] = strval($limit);
        }

        return $this->avata->request($this->getActionUrl('accounts'), $body, 'GET');
    }

    /**
     * 查询链账户操作记录
     * @param string $account 链账户地址
     * @param string $tx_hash Tx Hash
     * @param string $module 功能模块：nft / mt
     * @param string $operation 操作类型，仅 module 不为空时有效，默认为 "all"。
     *                          module = nft 时，可选：issue_class / transfer_class / mint / edit / transfer / burn；
     *                          module = mt 时，可选： issue_class / transfer_class / issue / mint / edit / transfer / burn。
     * @param string $start_date 日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_data 日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param int $offset 游标，默认为 0
     * @param int $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryChainAccountHistory(
        string $account,
        string $tx_hash,
        string $module = 'nft',
        string $operation = 'all',
        string $start_date = '',
        string $end_data = '',
        string $sort_by = 'DATE_ASC',
        $offset = 0,
        $limit = 10
    ): Response
    {
        $body = [];
        $body['account'] = $account;
        $body['tx_hash'] = $tx_hash;
        $body['module'] = $module;
        $body['operation'] = $operation;

        if($start_date !== ''){
            $body['start_date'] = date('Y-m-d', strtotime($start_date));
        }

        if($end_data !== ''){
            $body['end_data'] = date('Y-m-d', strtotime($end_data));
        }

        if($sort_by !== ''){
            $body['sort_by'] = $sort_by;
        }

        if($offset){
            $body['offset'] = strval($offset);
        }

        if($limit){
            $body['limit'] = strval($limit);
        }
        return $this->avata->request($this->getActionUrl('accounts'), $body, 'GET');
    }
}