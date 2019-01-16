<?php

namespace Akamap\BitrixRestApi;


/**
 * Bitrix Rest Api Library Class
 * @version 1.0
 */
class Connection
{
    protected
        $key,
        $authType,
        $apiUserId,
        $endpointUrlTemplate;

    /**
     * Connection constructor.
     *
     * @param string $endpoint
     * @param string $authType
     * @param string $token
     * @param int $apiUserId
     */
    public function __construct(string $endpoint, string $authType, string $token, int $apiUserId = 0)
    {
        if (empty($endpoint) || empty($token)) {
            throw new \InvalidArgumentException('endpoint or key is empty');
        }

        switch ($authType) {
            case 'webhook':
                if ($apiUserId > 0) {
                    $this->apiUserId = $apiUserId;
                    $this->endpointUrlTemplate = "https://{$endpoint}/rest/{$apiUserId}/{$token}/%s";
                } else {
                    throw new \InvalidArgumentException('apiUserId must be grater than 0 when webhook auth type is used');
                }
                break;

            case 'oauth':
                $this->endpointUrlTemplate = "https://{$endpoint}/rest/%s?auth={$token}";
                break;

            default:
                throw new \InvalidArgumentException('Invalid auth type');
        }

        $this->authType = $authType;
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getApiUserId(): int
    {
        return $this->apiUserId;
    }

    /**
     * @param string $action
     * @return string
     */
    protected function prepareQueryUrl(string $action): string
    {
        return sprintf($this->endpointUrlTemplate, $action);
    }

    /**
     * @param string $action
     * @param array $arguments
     * @param bool $debug
     * @return mixed
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function call(string $action, array $arguments = [], $debug = false)
    {
        $queryUrl = $this->prepareQueryUrl($action);
        $queryFields = http_build_query($arguments);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryFields,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);
        if (json_last_error()) {
            throw new \RuntimeException('json_decode failed: ' . json_last_error_msg());
        }

        if (isset($response['error'])) {
            throw new \BadMethodCallException($response['error']);
        }

        return $debug ? $response : $response['result'];
    }

    /**
     * @param string $method
     * @return string
     */
    static protected function fetchToAction(string $method): string
    {
        $action = implode('.', array_map(
                'strtolower', preg_split(
                    '/([A-Z][^A-Z]+)/', $method, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
                )
            )
        );

        return $action;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $arguments = [])
    {
        $action = static::fetchToAction($method);

        return $this->call($action, $arguments);
    }
}