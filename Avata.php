<?php
namespace Avata;

use Avata\AvataException;

class Avata
{

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function request($path, array $body = [], $method = 'GET')
    {
        $method = strtoupper($method);
        $url = rtrim($this->config->getDomain(), '/') . '/' . ltrim($path, '/');
        $timestamp = $this->millisecond();

        $param['path_url'] = $path;
        $ch = curl_init();
        switch ($method) {
            case 'GET':
                $url .= $body ? '?' . http_build_query($body) : '';
                if($body){
                    foreach ($body as $key => $value) {
                        $param['query_' . $key] = $value;
                    }
                }
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                if($body){
                    foreach ($body as $key => $value) {
                        $param['body_' . $key] = $value;
                    }
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if($body){
                    foreach ($body as $key => $value) {
                        $param['body_' . $key] = $value;
                    }
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if($body){
                    foreach ($body as $key => $value) {
                        $param['body_' . $key] = $value;
                    }
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if($body){
                    foreach ($body as $key => $value) {
                        $param['body_' . $key] = $value;
                    }
                }
                break;
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

        $data = curl_exec($ch);

        $errInfo = curl_error($ch);
        if($errInfo !== ''){
            throw new AvataException('avata curl error:' . $errInfo);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($data, true);
        return $data;
    }

    /**
     * 毫秒级时间戳
     * @return float
     */
    private function millisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)));
    }
}