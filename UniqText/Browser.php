<?php

class Browser
{
    private $http;
    private $proxy;
    private $connect_timeout;
    private $ch;

    function __construct($connect_timeout, $http, $proxy)
    {
        $this->connect_timeout = $connect_timeout;
        $this->http = $http;
        $this->proxy = $proxy;
    }

    /**
     * @return string
     */
    public function get()
    {
        $this->initCH();
        $data = curl_exec($this->ch);
        curl_close($this->ch);
        return $data;
    }

    public function post($parameter)
    {
        $this->initCH();
        curl_setopt($this->ch, CURLOPT_POST, true);
        if ($parameter != null) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $parameter);
        }
        $data = curl_exec($this->ch);
        curl_close($this->ch);
        return $data;
    }

    private function initCH()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->http);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0');
        $this->initProxy();
        $this->initTimeout();
    }

    private function initProxy()
    {
        if ($this->proxy != null) {
            curl_setopt($this->ch, CURLOPT_URL, $this->proxy);
        }
    }

    private function initTimeout()
    {
        if ($this->connect_timeout != null) {
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        }
    }

    public function setConnectTimeout($connect_timeout)
    {
        $this->connect_timeout = $connect_timeout;
    }

    public function setHttp($http)
    {
        $this->http = $http;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }
}