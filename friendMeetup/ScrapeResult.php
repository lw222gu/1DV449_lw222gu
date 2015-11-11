<?php

class ScrapeResult{

    private $url;

    public function __construct($url){
        $this->url = $url;
    }

    public function getUrl(){
        return $this->url;
    }

}