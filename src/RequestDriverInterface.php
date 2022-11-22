<?php
namespace Avata;

/**
 * Interface RequestDriverInterface
 * @package Avata
 */
interface RequestDriverInterface
{
    /**
     *
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     */
    public function get(string $url, array $body, array $header): Response;

    /**
     * post
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     */
    public function post(string $url, array $body, array $header): Response;

    /**
     * patch
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     */
    public function patch(string $url, array $body, array $header): Response;

    /**
     * put
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     */
    public function put(string $url, array $body, array $header): Response;

    /**
     * delete
     * @param string $url
     * @param array $body
     * @param array $header
     * @return Response
     */
    public function delete(string $url, array $body, array $header): Response;
}