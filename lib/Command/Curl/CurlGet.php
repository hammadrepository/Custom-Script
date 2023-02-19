<?php

namespace OCA\StcCustomScripts\Command\Curl;

class CurlGet
{
    private $url;
    private $options;
    private $headers;
           
    /**
     * @param string $url     Request URL
     * @param array  $options cURL options
     */
    public function __construct($url, array $options = [], array $headers = [])
    {
        $this->url = $url;
        $this->options = $options;
        $this->headers = $headers;
    }

    /**
     * Get the response
     * @return string
     * @throws \RuntimeException On cURL error
     */
    public function __invoke(array $post)
    {
        $ch = \curl_init($this->url);
        $username = 'admin3';
        $password = 'hammad123123123';
        foreach ($this->options as $key => $val) {
            \curl_setopt($ch, $key, $val);
        }
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        // \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers );
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $post);

        $response = \curl_exec($ch);
        $error    = \curl_error($ch);
        $errno    = \curl_errno($ch);
        
        if (\is_resource($ch)) {
            \curl_close($ch);
        }

        if (0 !== $errno) {
            throw new \RuntimeException($error, $errno);
        }
        
        return $response;
    }
}