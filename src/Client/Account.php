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
     * 链账户是应用方或其用户在区块链上的账户地址，用于存储和管理在区块链上所拥有的资产
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
     * 链账户是应用方或其用户在区块链上的账户地址，用于存储和管理在区块链上所拥有的资产
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
     * 可根据文档中给出的具体的查询条件查询和获取与应用方某一项目 ID 相互绑定的链账户地址
     * @param string $account 链账户地址
     * @param string $name 链账户名称，支持模糊查询
     * @param string $operation_id 操作 ID。此操作 ID 需要填写在请求创建链账户/批量创建链账户接口时，返回的 Operation ID
     * @param string $start_date 日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date 日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryChainAccount(
        string $account = '',
        string $name = '',
        string $operation_id = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('account', $account, $body);
        $this->setBodyParam('name', $name, $body);
        $this->setBodyParam('operation_id', $operation_id, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('accounts'), $body);
    }

    /**
     * 查询链账户操作记录
     * 查询具体某一个链账户在区块链上的相关操作记录及详情信息
     * @param string $account 链账户地址
     * @param string $tx_hash Tx Hash
     * @param string $module 功能模块：nft / mt
     * @param string $operation 操作类型，仅 module 不为空时有效，默认为 "all"。
     *                          module = nft 时，可选：issue_class / transfer_class / mint / edit / transfer / burn；
     *                          module = mt 时，可选： issue_class / transfer_class / issue / mint / edit / transfer / burn。
     * @param string $start_date 日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date 日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryChainAccountHistory(
        string $account,
        string $tx_hash,
        string $module = 'nft',
        string $operation = 'all',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body['account'] = $account;
        $body['tx_hash'] = $tx_hash;
        $body['module'] = $module;
        $body['operation'] = $operation;
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('accounts/history'), $body);
    }
}