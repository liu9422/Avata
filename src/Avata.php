<?php
namespace Avata;

/**
 * Class Avata
 * @package Avata
 */
class Avata
{
    protected $config;

    /**
     * Avata constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
        $method = strtoupper($method);
        $url = rtrim($this->config->getDomain(), '/') . '/' . ltrim($path, '/');
        $timestamp = $this->millisecond();

        $param['path_url'] = $path;
        $ch = curl_init();
        switch ($method) {
            case 'GET':
                $url .= $body ? '?' . http_build_query($body) : '';
                $this->getParamByBody($param, $body, 'query');
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
                $this->getParamByBody($param, $body);
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
                $this->getParamByBody($param, $body);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
                $this->getParamByBody($param, $body);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
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

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'X-Api-Key:' . $this->config->getApiKey(),
            'X-Signature:' . $hexHash,
            'X-Timestamp:' . $timestamp,
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $rawData = curl_exec($ch);

        $errInfo = curl_error($ch);
        if($errInfo !== ''){
            throw new AvataException('avata curl error:' . $errInfo);
        }


        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = new Response();
        $response->setHttpCode($httpCode);
        $response->setRaw($rawData);

        $data = json_decode($rawData, true);
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