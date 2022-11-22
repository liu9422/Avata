<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

/**
 * Class Recharge
 * @package Avata\Client
 */
class Recharge extends Client
{
    /**
     * Recharge constructor.
     * @param Avata $avata
     */
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    /**
     * 购买能量值/业务费
     * 通过 Avata 平台创建的 DDC 链账户，可以通过此接口进行能量值和业务费的购买。
     * 如果您是 BSN 文昌链-天舟平台非托管模式项目，可以使用该项目参数，通过此接口进行能量值的购买。
     * @param string $account 链账户地址
     * @param int $amount 购买金额 ，只能购买整数元金额；单位：分
     * @param string $order_type 充值类型：gas：能量值；business：业务费
     * @param string $order_id 自定义订单流水号，必需且仅包含数字、下划线及英文字母大/小写
     * @return Response
     * @throws AvataException
     */
    public function buyEnergy(
        string $account,
        int $amount,
        string $order_type,
        string $order_id
    ): Response
    {
        $body['account'] = $account;
        $body['amount'] = $amount;
        $body['order_type'] = $order_type;
        $body['order_id'] = $order_id;
        return $this->avata->request($this->getActionUrl('orders'), $body, 'POST');
    }

    /**
     * 查询能量值/业务费购买结果列表
     * 根据查询条件，展示与应用相关的能量值/业务费购买信息
     * @param string $status 订单状态：success 充值成功 / failed 充值失败 / pending 正在充值
     * @param string $start_date NFT 类别创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date NFT 类别创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryEnergyList(
        string $status = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('status', $status, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('orders'), $body);
    }

    /**
     * 查询能量值/业务费购买结果
     * 根据指定的 OrderID，查询相关的订单信息。
     * @param string $order_id Order ID
     * @return Response
     * @throws AvataException
     */
    public function queryEnergyDetail(string $order_id): Response
    {
        return $this->avata->request($this->getActionUrl('orders/' . $order_id));
    }

    /**
     * 批量购买能量值
     * 如果您是 BSN 文昌链-天舟平台非托管模式项目，可以使用该项目参数，通过此接口对多地址进行能量值的批量购买
     * @param string $order_id 自定义订单流水号，必需且仅包含数字、下划线及英文字母大/小写
     * @param array $list 充值信息
     * @return Response
     * @throws AvataException
     */
    public function batchBuyEnergy(string $order_id, array $list): Response
    {
        $body['order_id'] = $order_id;
        $body['list'] = $list;
        return $this->avata->request($this->getActionUrl('orders/batch'), $body, 'POST');
    }
}