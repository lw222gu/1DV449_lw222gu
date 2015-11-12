<?php

class ScrapeResult{

    private $url;
    private $data;
    private $result = "";
    private $calendarLink;
    private $cinemaLink;
    private $restaurantLink;

    public function __construct($url){
        $this->url = $url;
        $this->curlRequest();
        $this->getLinks();
    }

    private function curlRequest(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->data = curl_exec($ch);
        curl_close($ch);
    }

    private function getLinks(){
        $dom = new DOMDocument();

        if($dom->loadHTML($this->data)){
            $xPath = new DOMXPath($dom);
            $links = $xPath->query('//ol//li/a');

            foreach ($links as $link){
                if($link->nodeValue == "Kalendrar"){
                    $this->calendarLink = $link->getAttribute("href");
                    continue;
                }

                if($link->nodeValue == "Stadens biograf!"){
                    $this->cinemaLink = $link->getAttribute("href");
                    continue;
                }

                if($link->nodeValue == "Zekes restaurang!"){
                    $this->restaurantLink = $link->getAttribute("href");
                    continue;
                }
            }
        }
        else {
            die("Fel vid inläsning av data.");
        }
    }

    public function getResult(){
        return $this->result;
    }

}