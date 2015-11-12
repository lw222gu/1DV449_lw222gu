<?php

class Meetup {

    private $result;
    private $url;
    private $scrape;
    private $startPageLinks;

    public function __construct($url){
        $this->url = $url;
        $this->scrape = new Scrape();

        //Get cURL request data and send it into getLinks
        $this->startPageLinks = $this->scrape->getLinks($this->scrape->curlRequest($url));

        $this->getAvaliableCalendarDays();
    }

    private function getAvaliableCalendarDays(){
        $calendarUrl = $this->url . substr($this->startPageLinks["Kalendrar"], 1) . "/";
        $calendarLinks = $this->scrape->getLinks($this->scrape->curlRequest($calendarUrl));
    }

    private function getAvaliableMovies(){

    }

    private function getAvaliableTables(){

    }

    public function getResult(){
        return $this->result;
    }

}