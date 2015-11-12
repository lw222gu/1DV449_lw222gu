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

        $avaliableDays = $this->getAvaliableCalendarDays();
    }

    private function getAvaliableCalendarDays(){
        $calendarUrl = $this->url . substr($this->startPageLinks["Kalendrar"], 1) . "/";
        $calendarLinks = $this->scrape->getLinks($this->scrape->curlRequest($calendarUrl));

        $paulsCalendar = $calendarLinks["Paul calendar"];
        $petersCalendar = $calendarLinks["Peter calendar"];
        $marysCalendar = $calendarLinks["Mary calendar"];

        $paulIsAvailableAt = $this->scrape->getCalendar($this->scrape->curlRequest($calendarUrl . "/" . $paulsCalendar));
        $peterIsAvailableAt = $this->scrape->getCalendar($this->scrape->curlRequest($calendarUrl . "/" . $petersCalendar));
        $maryIsAvailableAt = $this->scrape->getCalendar($this->scrape->curlRequest($calendarUrl . "/" . $marysCalendar));

        $avaliableDays = array(false, false, false);

        if($paulIsAvailableAt[0] == true && $peterIsAvailableAt[0] == true && $maryIsAvailableAt[0] == true){
            $avaliableDays[0] = true;
        }

        if($paulIsAvailableAt[1] == true && $peterIsAvailableAt[1] == true && $maryIsAvailableAt[1] == true){
            $avaliableDays[1] = true;
        }

        if($paulIsAvailableAt[2] == true && $peterIsAvailableAt[2] == true && $maryIsAvailableAt[2] == true){
            $avaliableDays[2] = true;
        }

        return $avaliableDays;
    }

    private function getAvaliableMovies(){

    }

    private function getAvaliableTables(){

    }

    public function getResult(){
        return $this->result;
    }

}