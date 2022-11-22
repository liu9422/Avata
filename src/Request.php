<?php
namespace Avata;

class Request implements RequestDriverInterface
{
    /**
     * get
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    public function get(string $url, array $body, array $header): Response
    {
        $url .= $body ? '?' . http_build_query($body) : '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        return $this->createResponse($ch, $header);
    }

    /**
     * post
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    public function post(string $url, array $body, array $header) : Response
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
        return $this->createResponse($ch, $header);
    }

    /**
     * patch
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    public function patch(string $url, array $body, array $header) : Response
    {
        return $this->createCustomerQuest($url, 'PATCH', $body, $header);
    }

    /**
     * put
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    public function put(string $url, array $body, array $header) : Response
    {
        return $this->createCustomerQuest($url, 'PUT', $body, $header);
    }

    /**
     * delete
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    public function delete(string $url, array $body, array $header) : Response
    {
        return $this->createCustomerQuest($url, 'DELETE', $body, $header);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $body
     * @param array $header
     * @return Response
     * @throws AvataException
     */
    private function createCustomerQuest(string $url, string $method, array $body, array $header) : Response
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body ? json_encode($body) : '');
        return $this->createResponse($ch, $header);
    }

    /**
     * @param $curl
     * @param array $header
     * @param int $timeout
     * @return Response
     * @throws AvataException
     */
    private function createResponse($curl, array $header, int $timeout = 3): Response
    {
        $headerParam = [];
        if($header){
            foreach ($header as $key => $value) {
                $headerParam[] = $key . ':' . $value;
            }
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerParam);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $rawData = curl_exec($curl);
        $errInfo = curl_error($curl);
        if($errInfo !== ''){
            throw new AvataException('avata curl error:' . $errInfo);
        }
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response();
        $response->setHttpCode($httpCode);
        $response->setRaw($rawData);

        return $response;
    }
}