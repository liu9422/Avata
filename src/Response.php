<?php
namespace Avata;

/**
 * Class Response
 * @package Avata
 */
class Response
{
    protected $httpCode;
    protected $data;
    protected $raw;
    protected $error;
    protected $errCode;
    protected $errSpace;
    protected $errMsg;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getErrSpace()
    {
        return $this->errSpace;
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param mixed $errCode
     */
    public function setErrCode($errCode): void
    {
        $this->errCode = $errCode;
    }

    /**
     * @param mixed $errMsg
     */
    public function setErrMsg($errMsg): void
    {
        $this->errMsg = $errMsg;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @param mixed $errSpace
     */
    public function setErrSpace($errSpace): void
    {
        $this->errSpace = $errSpace;
    }

    /**
     * @param mixed $httpCode
     */
    public function setHttpCode($httpCode): void
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @param mixed $raw
     */
    public function setRaw($raw): void
    {
        $this->raw = $raw;
    }
}