<?php

namespace CBR;

use RuntimeException;

class Request
{
    /** @var string */
    private $url;

    /**
     * @param string                $url
     * @param array<string, string> $data
     */
    public function __construct($url, $data = [])
    {
        $this->url = $url . ((empty($data)) ? '' : '?' . http_build_query($data));
    }

    /**
     * Выполнение запроса.
     *
     * @return string
     */
    public function request()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new RuntimeException($error);
        }

        $http_code = $info['http_code'];
        $http_code_group = (int)floor($http_code / 100);

        if ($http_code_group !== 2) {
            throw new RuntimeException($result, $http_code);
        }

        return $result;
    }
}
