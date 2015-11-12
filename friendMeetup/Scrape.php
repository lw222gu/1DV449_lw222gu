<?php

class Scrape{

    private $data;

    public function __construct(){
        //$this->curlRequest($url);
        //$this->getLinks();
    }

    public function curlRequest($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function getXPath($data){
        $dom = new DOMDocument();

        if($dom->loadHTML($data)){
            $xPath = new DOMXPath($dom);
            return $xPath;
        }
        else {
            die("Fel vid inläsning av data.");
        }
    }

    public function getLinks($data){
        $DOMLinks = $this->getXPath($data)->query('//a');
        $links = array();

        /*
         * Push links to links array as key-value pairs,
         * where nodeValue = key, and href attribute = value.
        */
        foreach ($DOMLinks as $DOMLink){
            $links[$DOMLink->nodeValue] = $DOMLink->getAttribute("href");
        }

        return $links;
    }
}