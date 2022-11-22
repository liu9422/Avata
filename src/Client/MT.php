<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

/**
 * Class MT
 * @package Avata\Client
 */
class MT extends Client
{
    /**
     * MT constructor.
     * @param Avata $avata
     */
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    /**
     * 创建 MT 类别
     * MT 类别是 IRITA 底层链对同一资产类型的识别和集合，方便资产发行方对链上资产进行管理和查询。所以链上资产在发行前，都需要创建 MT 类别，用以声明其抽象属性
     * @param string $name MT 类别名称
     * @param string $owner MT 类别权属者地址，支持任一 Avata 平台内合法链账户地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $data 自定义链上元数据
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function createMTCategory(
        string $name,
        string $owner,
        string $operation_id,
        string $data = '',
        array $tag = []
    ): Response
    {
        $body['name'] = $name;
        $body['owner'] = $owner;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl('mt/classes'), $body, 'POST');
    }

    /**
     * 查询 MT 类别
     * 根据查询条件，展示 Avata 平台内的 MT 类别列表
     * @param string $id MT 类别 ID
     * @param string $name MT 类别名称，支持模糊查询
     * @param string $owner MT 类别权属者地址
     * @param string $tx_hash 创建 MT 类别的 Tx Hash
     * @param string $start_date MT 类别创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date MT 类别创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryMTCategory(
        string $id = '',
        string $name = '',
        string $owner = '',
        string $tx_hash = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('id', $id, $body);
        $this->setBodyParam('name', $name, $body);
        $this->setBodyParam('owner', $owner, $body);
        $this->setBodyParam('tx_hash', $tx_hash, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('mt/classes'), $body);
    }

    /**
     * 查询 MT 类别详情
     * 根据查询条件，展示与 Avata 平台内的 MT 类别的详情信息
     * @param string $id MT 类别 ID
     * @return Response
     * @throws AvataException
     */
    public function queryMTCategoryDetail(string $id): Response
    {
        return $this->avata->request($this->getActionUrl('mt/classes/' . $id));
    }

    /**
     * 转让 MT 类别
     * MT 类别权属者（MT Class Owner），拥有在该 MT 类别中发行 MT 的权限和转让该 MT 类别的权限。
     * 注意：「Avata」API 服务平台「允许」应用平台方将 MT 类别转让给「任一 Avata 平台内合法链账户地址」。
     * @param string $class_id MT 类别 ID
     * @param string $owner MT 类别权属者地址
     * @param string $recipient MT 类别接收者地址，支持任一 Avata 平台内合法链账户地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function transferMTCategory(
        string $class_id,
        string $owner,
        string $recipient,
        string $operation_id,
        array $tag = []
    ): Response
    {
        $body['recipient'] = $recipient;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/class-transfers/%s/%s', $class_id, $owner)),
            $body,
            'POST'
        );
    }

    /**
     * 发行 MT
     * MT 类别权属者（MT Class Owner）通过调用此接口来发行 MT。单个 MT 的发行数量上限为 2^64-1。
     * @param string $class_id MT 类别 ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param int $amount MT 数量，不填写数量时，默认发行数量为 1
     * @param string $data 自定义链上元数据
     * @param string $recipient MT 接收者地址，支持任一文昌链合法链账户地址，默认为 MT 类别的权属者地址
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function createMT(
        string $class_id,
        string $operation_id,
        int $amount = 1,
        string $data = '',
        string $recipient = '',
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('amount', $amount, $body, null, 'intval');
        $this->setBodyParam('recipient', $recipient, $body);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/mt-issues/%s', $class_id)),
            $body,
            'POST'
        );
    }

    /**
     * 增发 MT
     * 当不填写接收者时，默认该类别的拥有者为接收者；
     * 当不填写增发数量时，默认增发数量为 1。
     * @param string $class_id MT 的类别 ID
     * @param string $mt_id MT 的 ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param int $amount MT 数量
     * @param string $recipient MT 接收者地址
     * @param array $tag 交易标签, 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function addMT(
        string $class_id,
        string $mt_id,
        string $operation_id,
        int $amount = 1,
        string $recipient = '',
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('amount', $amount, $body, null, 'intval');
        $this->setBodyParam('recipient', $recipient, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/mt-mints/%s/%s', $class_id, $mt_id)),
            $body,
            'POST'
        );
    }

    /**
     * 转让 MT
     * MT 的拥有者可以向指定的链账户地址转移指定数量的 MT，目标转移地址可以是文昌链的任一合法地址。
     * @param string $class_id MT 类别 ID
     * @param string $owner MT 持有者地址
     * @param string $mt_id MT ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $recipient 接收者地址
     * @param int $amount 转移的数量（默认为 1 ）
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function transferMT(
        string $class_id,
        string $owner,
        string $mt_id,
        string $operation_id,
        string $recipient,
        int $amount = 1,
        array $tag = []
    ): Response
    {
        $body['recipient'] = $recipient;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('amount', $amount, $body, null, 'intval');
        $this->setBodyParam('recipient', $recipient, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/mt-transfers/%s/%s/%s', $class_id, $owner, $mt_id)),
            $body,
            'POST'
        );
    }

    /**
     * 编辑 MT
     * MT 类别权属者（MT Class Owner）通过调用此接口可以修改链上 MT 的元数据。
     * @param string $class_id MT 类别 ID
     * @param string $owner MT 持有者地址
     * @param string $mt_id MT ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $data 自定义链上元数据
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function editMT(
        string $class_id,
        string $owner,
        string $mt_id,
        string $operation_id,
        string $data,
        array $tag = []
    ): Response
    {
        $body['data'] = $data;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/mts/%s/%s/%s', $class_id, $owner, $mt_id)),
            $body,
            'PATCH'
        );
    }

    /**
     * 销毁 MT
     * MT 的拥有者可以销毁自己链账户地址中的某一个 MT。其中，销毁的最大数量为实际拥有该 MT 的数量。 注：当销毁的数量为 0 时，默认为 1。
     * @param string $class_id MT 类别 ID
     * @param string $owner MT 持有者地址
     * @param string $mt_id MT ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param int $amount 转移的数量（默认为 1 ）
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function deleteMT(
        string $class_id,
        string $owner,
        string $mt_id,
        string $operation_id,
        int $amount = 1,
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('amount', $amount, $body, null, 'intval');
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('mt/mts/%s/%s/%s', $class_id, $owner, $mt_id)),
            $body,
            'DELETE'
        );
    }

    /**
     * 查询 MT
     * 根据查询条件，展示 Avata 平台内的 MT 列表
     * @param string $id MT ID
     * @param string $class_id MT 类别 ID
     * @param string $issuer MT 发行者地址
     * @param string $tx_hash 创建 MT 的 Tx Hash
     * @param string $start_date MT 创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date MT 创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryMT(
        string $id = '',
        string $class_id = '',
        string $issuer = '',
        string $tx_hash = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC ',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('id', $id, $body);
        $this->setBodyParam('class_id', $class_id, $body);
        $this->setBodyParam('issuer', $issuer, $body);
        $this->setBodyParam('tx_hash', $tx_hash, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('mt/mts'), $body);
    }

    /**
     * 查询 MT 详情
     * 根据查询条件，展示 Avata 平台内的 MT 对应的详情信息
     * @param string $class_id MT 类别 ID
     * @param string $mt_id MT ID
     * @return Response
     * @throws AvataException
     */
    public function queryMTDetail(string $class_id, string $mt_id): Response
    {
        return $this->avata->request(
            $this->getActionUrl(sprintf('mt/mts/%s/%s', $class_id, $mt_id))
        );
    }

    /**
     * 查询 MT 操作记录
     * 根据查询条件，展示与应用相关的 MT 链上操作记录（以 Tx Msg 作为列表元素，不展示 Tx Fee，费用管理准备提供独立的 API 及 Console）
     * @param string $class_id MT 类别 ID
     * @param string $mt_id MT ID
     * @param string $signer Tx 签名者地址
     * @param string $tx_hash MT 操作 Tx Hash
     * @param string $operation 操作类型： issue(首发MT) / mint(增发MT) / edit(编辑MT) / transfer(转让MT) / burn(销毁MT)
     * @param string $start_date MT 操作日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date MT 操作日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryMTHistory(
        string $class_id,
        string $mt_id,
        string $signer = '',
        string $tx_hash = '',
        string $operation = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC ',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('signer', $signer, $body);
        $this->setBodyParam('tx_hash', $tx_hash, $body);
        $this->setBodyParam('operation', $operation, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request(
            $this->getActionUrl(
                sprintf('mt/mts/%s/%s/history', $class_id, $mt_id)
            )
        );
    }

    /**
     * 查询 MT 余额
     * 根据查询条件，展示链账户拥有的 MT 余额(AVATA 平台内)
     * @param string $class_id MT 类别ID
     * @param string $account 链账户地址
     * @param string $id MT ID
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryMTBalance(
        string $class_id,
        string $account,
        string $id = '',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('id', $id, $body);
        $this->setBodyParam('offset', $offset, $body, null, 'strval');
        $this->setBodyParam('limit', $limit, $body, null, 'strval');
        return $this->avata->request(
            $this->getActionUrl(
                sprintf('mt/mts/%s/%s/balance', $class_id, $account)
            )
        );
    }
}