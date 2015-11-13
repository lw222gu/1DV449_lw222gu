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
        $avaliableMovies = $this->getAvaliableMovies($avaliableDays);
    }

    private function getAvaliableCalendarDays(){
        $calendarUrl = $this->url . substr($this->startPageLinks["Kalendrar"], 1) . "/";
        $calendarLinks = $this->scrape->getLinks($this->scrape->curlRequest($calendarUrl));

        $allCalendars = array();

        foreach($calendarLinks as $link){
            $personalCalendar = $this->scrape->getCalendar($this->scrape->curlRequest($calendarUrl . "/" . $link));
            array_push($allCalendars, $personalCalendar);
        }

        $days = array(0, 0, 0);

        foreach($allCalendars as $calendar){
            for($i = 0; $i < count($calendar); $i = $i+1){
                $days[$i] += $calendar[$i];
            }
        }

        $availableDays = array();

        if($days[0] == count($allCalendars)){
            array_push($availableDays, "Fredag");
        }
        if($days[1] == count($allCalendars)){
            array_push($availableDays, "Lördag");
        }
        if($days[2] == count($allCalendars)){
            array_push($availableDays, "Söndag");
        }

        return $availableDays;
    }

    private function getAvaliableMovies($days){
        $cinemaUrl = $this->url . substr($this->startPageLinks["Stadens biograf!"], 1);
        $movieDays = $this->scrape->getMovieDays($this->scrape->curlRequest($cinemaUrl));

        foreach($movieDays as $day){
            if(in_array($day, $days)){
                //Skrapa filmer från den dagen!
            }
        }

        $movies = null;
        return $movies;
    }

    private function getAvaliableTables(){

    }

    public function getResult(){
        return $this->result;
    }

}