<?php

class ScrapeResult{

    private $url;

    public function __construct($url){
        $this->url = $url;
    }

    public function curlRequest(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}