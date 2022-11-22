<?php
namespace Avata\Client;

use Avata\Avata;
use Avata\AvataException;
use Avata\Response;

/**
 * Class NFT
 * @package Avata\Client
 */
class NFT extends Client
{
    /**
     * NFT constructor.
     * @param Avata $avata
     */
    public function __construct(Avata $avata)
    {
        parent::__construct($avata);
    }

    /**
     * 创建 NFT 类别
     * NFT 类别是 IRITA 底层链对同一资产类型的识别和集合，方便资产发行方对链上资产进行管理和查询。所以在发行 NFT 前，都需要创建 NFT 类别，用以声明其抽象属性。
     * @param string $name NFT 类别名称
     * @param string $owner NFT 类别权属者地址，拥有在该 NFT 类别中发行 NFT 的权限和转让该 NFT 类别的权限。
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $class_id NFT 类别 ID，仅支持小写字母及数字，以字母开头
     * @param string $symbol 标识
     * @param string $description 描述
     * @param string $uri 链外数据链接
     * @param string $uri_hash 链外数据 Hash
     * @param string $data 自定义链上元数据
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
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
        array $tag = []
    ): Response
    {
        $body['name'] = $name;
        $body['owner'] = $owner;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('class_id', $class_id, $body);
        $this->setBodyParam('symbol', $symbol, $body);
        $this->setBodyParam('description', $description, $body);
        $this->setBodyParam('uri', $uri, $body);
        $this->setBodyParam('uri_hash', $uri_hash, $body);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl('nft/classes'), $body, 'POST');
    }

    /**
     * 查询 NFT 类别
     * 根据查询条件，展示 Avata 平台内的 NFT 类别列表
     * @param string $id NFT 类别 ID
     * @param string $name NFT 类别名称，支持模糊查询
     * @param string $owner NFT 类别权属者地址
     * @param string $tx_hash 创建 NFT 类别的 Tx Hash
     * @param string $start_date NFT 类别创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date NFT 类别创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryNFTCategory(
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
        return $this->avata->request($this->getActionUrl('nft/classes'), $body);
    }

    /**
     * 查询 NFT 类别详情
     * 根据查询条件，展示 Avata 平台内的 NFT 类别的详情信息
     * @param string $id NFT 类别 ID
     * @return Response
     * @throws AvataException
     */
    public function queryNFTCategoryDetail(string $id): Response
    {
        return $this->avata->request($this->getActionUrl('nft/classes/' . $id));
    }

    /**
     * 转让 NFT 类别
     * NFT 类别权属者（NFT Class Owner），拥有在该 NFT 类别中发行 NFT 的权限和转让该 NFT 类别的权限。 注意：「Avata」API 服务平台「允许」应用平台方将 NFT 类别转让给「任一 Avata 平台内合法链账户地址」。
     * @param string $class_id NFT 类别 ID
     * @param string $owner NFT 类别权属者地址
     * @param string $recipient NFT 类别接收者地址，支持任一 Avata 平台内合法链账户地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function transfersNFTCategory(
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
            sprintf('nft/class-transfers/%s/%s', $class_id, $owner)),
            $body,
            'POST'
        );
    }

    /**
     * 发行 NFT
     * NFT 是链上唯一的、可识别的资产，由用户自己在区块链上铸造一个NF
     * @param string $class_id NFT 类别 ID
     * @param string $name NFT 名称
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $uri 链外数据链接
     * @param string $uri_hash 链外数据 Hash
     * @param string $data 自定义链上元数据
     * @param string $recipient NFT 接收者地址，支持任一文昌链合法链账户地址，默认为 NFT 类别的权属者地址
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function createNFT(
        string $class_id,
        string $name,
        string $operation_id,
        string $uri = '',
        string $uri_hash = '',
        string $data = '',
        string $recipient = '',
        array $tag = []
    ): Response
    {
        $body['name'] = $name;
        $body['operation_id'] = $this->getOperationId($operation_id);
        $this->setBodyParam('uri', $uri, $body);
        $this->setBodyParam('uri_hash', $uri_hash, $body);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('recipient', $recipient, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/nfts/%s', $class_id)),
            $body,
            'POST'
        );
    }

    /**
     * 转让 NFT
     * 注意：在调用此接口时，「Avata」API 服务平台「允许」应用平台方将 NFT 转让给「任一文昌链合法链账户地址」。
     * @param string $class_id NFT 类别 ID
     * @param string $owner NFT 持有者地址
     * @param string $nft_id NFT ID
     * @param string $recipient NFT 接收者地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function transfersNFT(
        string $class_id,
        string $owner,
        string $nft_id,
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
            sprintf('nft/nft-transfers/%s/%s/%s', $class_id, $owner, $nft_id)),
            $body,
            'POST'
        );
    }

    /**
     * 编辑 NFT
     * 对于已经发布至链上的 NFT ，可根据需求来编辑链上 NFT 的相关信息，如名称、元数据等信息。注意：只可编辑自己链账户所拥有的 NFT 信息，对于由自己发行，但已经归属于其他链账户地址的 NFT，不可进行编辑。
     * @param string $class_id NFT 类别 ID
     * @param string $owner NFT 持有者地址
     * @param string $nft_id NFT ID
     * @param string $name NFT 名称
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param string $uri 链外数据链接
     * @param string $data 自定义链上元数据
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function editNFT(
        string $class_id,
        string $owner,
        string $nft_id,
        string $name,
        string $operation_id,
        string $uri = '',
        string $data = '',
        array $tag = []
    ): Response
    {
        $body['name'] = $name;
        $body['operation_id'] = $operation_id;
        $this->setBodyParam('uri', $uri, $body);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/nfts/%s/%s/%s', $class_id, $owner, $nft_id)),
            $body,
            'PATCH'
        );
    }

    /**
     * 销毁 NFT
     * 用户可以销毁自己链账户地址中拥有的 NFT 。NFT 销毁后，链上依然会保留与该 NFT 相关的链上历史记录信息，但不可再对该 NFT 进行任何的操作。
     * @param string $class_id NFT 类别 ID
     * @param string $owner NFT 持有者地址
     * @param string $nft_id NFT ID
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function deleteNFT(
        string $class_id,
        string $owner,
        string $nft_id,
        string $operation_id,
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $operation_id;
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/nfts/%s/%s/%s', $class_id, $owner, $nft_id)),
            $body,
            'DELETE'
        );
    }

    /**
     * 批量发行 NFT
     * NFT 是链上唯一的、可识别的资产，由用户自己在区块链上批量铸造 NFT，单次请求批量发行数量上限10。
     * 使用批量发行 NFT 接口时，需保证此交易体小于 7000 字节，如果交易体数据很难估算准确，建议避免使用批量发行方法。
     * 由于批量发行方法对网络平滑出块影响比较大，后续其 GAS 费有可能被调高以不鼓励批量发行方法的使用。
     * @param string $class_id NFT 类别 ID
     * @param string $name NFT 名称
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $recipients NFT 接收者地址和发行数量。以数组的方式进行组合，可以自定义多个组合，可面向多地址批量发行 NFT
     * @param string $uri 链外数据链接
     * @param string $uri_hash 链外数据 Hash
     * @param string $data 自定义链上元数据
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function batchCreateNFT(
        string $class_id,
        string $name,
        string $operation_id,
        array $recipients,
        string $uri = '',
        string $uri_hash = '',
        string $data = '',
        array $tag = []
    ): Response
    {
        $body['name'] = $name;
        $body['operation_id'] = $operation_id;
        $body['recipients'] = $recipients;
        $this->setBodyParam('uri', $uri, $body);
        $this->setBodyParam('uri_hash', $uri_hash, $body);
        $this->setBodyParam('data', $data, $body);
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/batch/nfts/%s', $class_id)),
            $body,
            'POST'
        );
    }

    /**
     * 批量转让 NFT
     * NFT 的拥有者可以调用该接口批量转让其 NFT，批量转让的 NFT 数量上限为 10。
     * 使用批量转让 NFT 接口时，需保证此交易体小于 7000 字节，如果交易体数据很难估算准确，建议避免使用批量发行方法。
     * 由于批量发行方法对网络平滑出块影响比较大，后续其 GAS 费有可能被调高以不鼓励批量发行方法的使用。
     * 注意：在调用此接口时，「Avata」API 服务平台「允许」应用平台方将 NFT 转让给「任一文昌链合法链账户地址」。
     * @param string $owner NFT 持有者地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $data Array of objects
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function batchTransferNFT(
        string $owner,
        string $operation_id,
        array $data,
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $operation_id;
        $body['data'] = $data;
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/batch/nft-transfers/%s', $owner)),
            $body,
            'POST'
        );
    }

    /**
     * 批量编辑 NFT
     * NFT 的拥有者可以调用该接口批量编辑其 NFT。其中，可编辑的参数为 name 、uri 和 data，批量编辑的 NFT 数量上限为 10。
     * 使用批量编辑 NFT 接口时，需保证此交易体小于 7000 字节，如果交易体数据很难估算准确，建议避免使用批量发行方法。
     * 由于批量发行方法对网络平滑出块影响比较大，后续其 GAS 费有可能被调高以不鼓励批量发行方法的使用。
     * 注意：只可编辑自己链账户所拥有的 NFT ，对于由自己发行，但已经归属于其他链账户地址的 NFT，不可进行编辑。
     * @param string $owner NFT 持有者地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $nfts Array of objects
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function batchEditNFT(
        string $owner,
        string $operation_id,
        array $nfts,
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $operation_id;
        $body['nfts'] = $nfts;
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/batch/nfts/%s', $owner)),
            $body,
            'PATCH'
        );
    }

    /**
     * 批量销毁 NFT
     * NFT 的拥有者可以调用该接口批量销毁其 NFT，批量销毁的 NFT 数量上限为 10。
     * 使用批量销毁 NFT 接口时，需保证此交易体小于 7000 字节，如果交易体数据很难估算准确，建议避免使用批量发行方法。
     * 由于批量发行方法对网络平滑出块影响比较大，后续其 GAS 费有可能被调高以不鼓励批量发行方法的使用。
     * 注意：NFT 销毁后，链上依然会保留与该 NFT 相关的链上历史记录信息，但不可再对该 NFT 进行任何的操作。
     * @param string $owner NFT 持有者地址
     * @param string $operation_id 操作 ID，保证幂等性，避免重复请求，保证对于同一操作发起的一次请求或者多次请求的结果是一致的；由接入方生成的、针对每个 Project ID 唯一的、不超过 64 个大小写字母、数字、-、下划线的字符串
     * @param array $nfts Array of objects
     * @param array $tag 交易标签， 自定义 key：支持大小写英文字母和汉字和数字，长度 6-12 位，自定义 value：长度限制在 64 位字符，支持大小写字母和数字
     * @return Response
     * @throws AvataException
     */
    public function batchDeleteNFT(
        string $owner,
        string $operation_id,
        array $nfts,
        array $tag = []
    ): Response
    {
        $body['operation_id'] = $operation_id;
        $body['nfts'] = $nfts;
        $this->setBodyParam('tag', $tag, $body, function ($value){
            return is_array($value) && $value;
        });
        return $this->avata->request($this->getActionUrl(
            sprintf('nft/batch/nfts/%s', $owner)),
            $body,
            'DELETE'
        );
    }

    /**
     * 查询 NFT
     * 根据查询条件，展示 Avata 平台内的 NFT 列表
     * @param string $id NFT ID
     * @param string $name NFT 名称，支持模糊查询
     * @param string $class_id NFT 类别 ID
     * @param string $owner NFT 持有者地址
     * @param string $tx_hash 创建 NFT 的 Tx Hash
     * @param string $status NFT 状态：active / burned，默认为 active
     * @param string $start_date NFT 创建日期范围 - 开始，yyyy-MM-dd（UTC 时间
     * @param string $end_date NFT 创建日期范围 - 结束，yyyy-MM-dd（UTC 时间
     * @param string $sort_by 排序规则：DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryNFT(
        string $id = '',
        string $name = '',
        string $class_id = '',
        string $owner = '',
        string $tx_hash = '',
        string $status = '',
        string $start_date = '',
        string $end_date = '',
        string $sort_by = 'DATE_ASC ',
        string $offset = '0',
        string $limit = '10'
    ): Response
    {
        $body = [];
        $this->setBodyParam('id', $id, $body);
        $this->setBodyParam('name', $name, $body);
        $this->setBodyParam('class_id', $class_id, $body);
        $this->setBodyParam('owner', $owner, $body);
        $this->setBodyParam('tx_hash', $tx_hash, $body);
        $this->setBodyParam('status', $status, $body);
        $this->setCommonBody($body, $start_date, $end_date, $sort_by, $offset, $limit);
        return $this->avata->request($this->getActionUrl('nft/nfts'), $body);
    }

    /**
     * 查询 NFT 详情
     * 根据查询条件，展示 Avata 平台内的 NFT 对应的详情信息
     * @param string $class_id NFT 类别 ID
     * @param string $nft_id NFT ID
     * @return Response
     * @throws AvataException
     */
    public function queryNFTDetail(string $class_id, string $nft_id): Response
    {
        return $this->avata->request(
            $this->getActionUrl(sprintf('nft/nfts/%s/%s', $class_id, $nft_id))
        );
    }

    /**
     * 查询 NFT 操作记录
     * 根据查询条件，展示与应用相关的 NFT 链上操作记录（以 Tx Msg 作为列表元素，不展示 Tx Fee，费用管理准备提供独立的 API 及 Console）
     * @param string $class_id NFT 类别 ID
     * @param string $nft_id NFT ID
     * @param string $signer Tx 签名者地址
     * @param string $tx_hash NFT 操作 Tx Hash
     * @param string $operation 操作类型：mint / edit / transfer / burn
     * @param string $start_date NFT 操作日期范围 - 开始，yyyy-MM-dd（UTC 时间）
     * @param string $end_date NFT 操作日期范围 - 结束，yyyy-MM-dd（UTC 时间）
     * @param string $sort_by DATE_ASC / DATE_DESC
     * @param string $offset 游标，默认为 0
     * @param string $limit 每页记录数，默认为 10，上限为 50
     * @return Response
     * @throws AvataException
     */
    public function queryNFTHistory(
        string $class_id,
        string $nft_id,
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
                sprintf('nft/nfts/%s/%s/history', $class_id, $nft_id)
            )
        );
    }
}