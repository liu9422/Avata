<?php
namespace Avata;

/**
 * Class Avata
 * @package Avata
 */
class Avata
{
    protected $config;

    protected $requestDriver;

    /**
     * Avata constructor.
     * @param Config $config
     * @param RequestDriverInterface|null $requestDriver
     */
    public function __construct(Config $config, RequestDriverInterface $requestDriver = null)
    {
        $this->config = $config;
        $this->requestDriver = is_null($requestDriver) ? new Request() : $requestDriver;
    }

    /**
     * @param string $path
     * @param array $body
     * @param string $method
     * @return Response
     * @throws AvataException
     */
    public function request(string $path, array $body = [], $method = 'GET'): Response
    {
        $method = strtolower($method);
        $url = rtrim($this->config->getDomain(), '/') . '/' . ltrim($path, '/');
        $timestamp = $this->millisecond();

        $param['path_url'] = $path;

        switch ($method) {
            case 'get':
                $this->getParamByBody($param, $body, 'query');
                break;
            case 'patch':
            case 'put':
            case 'delete':
            case 'post':
                $this->getParamByBody($param, $body);
                break;
            default:
                throw new AvataException(sprintf('avata curl error:%s method is not allowed', $method));
        }

        ksort($param);

        $hexHash = hash('sha256',
            stripcslashes(
                json_encode($param, JSON_UNESCAPED_UNICODE) . $timestamp . $this->config->getApiSecret()
            )
        );

        $header['Content-Type'] = 'application/json';
        $header['X-Api-Key'] = $this->config->getApiKey();
        $header['X-Signature'] = $hexHash;
        $header['X-Timestamp'] = $timestamp;

        /* @var $response Response */
        $response = $this->requestDriver->$method($url, $body, $header);

        $data = json_decode($response->getRaw(), true);
        if(isset($data['data'])){
            $response->setData($data['data']);
        }
        if(isset($data['error'])){
            $response->setError($data['error']);
            $response->setErrCode($data['error']['code'] ?? null);
            $response->setErrSpace($data['error']['code_space'] ?? null);
            $response->setErrMsg($data['error']['message'] ?? null);
        }
        return $response;
    }

    /**
     * @param $param
     * @param array $body
     * @param string $prefix
     */
    private function getParamByBody(&$param, array $body, $prefix = 'body')
    {
        if($body){
            foreach ($body as $key => $value) {
                $param[$prefix . '_' . $key] = $value;
            }
        }
    }

    /**
     * 时间戳
     * @return float
     */
    private function millisecond(): float
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)));
    }
}