<?php

class Meetup {

    private $result;
    private $url;
    private $scrape;
    private $startPageLinks;
    private $days = array("01" => "fredag", "02" => "lördag", "03" => "söndag");
    private $movies = array("01" => "Söderkåkar", "02" => "Fabian Bom", "03" => "Pensionat Paradiset");

    public function __construct($url){
        $this->url = $url;
        $this->scrape = new Scrape();

        //Get cURL request data and send it into getLinks
        $this->startPageLinks = $this->scrape->getLinks($this->scrape->curlRequest($url));

        $availableDays = $this->getAvailableCalendarDays();
        $availableMovieOccasions = $this->getAvailableMovies($availableDays);
    }

    private function getAvailableCalendarDays(){
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
            //array_push($availableDays, "Fredag");
            array_push($availableDays, "01");
        }
        if($days[1] == count($allCalendars)){
            //array_push($availableDays, "Lördag");
            array_push($availableDays, "02");
        }
        if($days[2] == count($allCalendars)){
            //array_push($availableDays, "Söndag");
            array_push($availableDays, "03");
        }

        return $availableDays;
    }

    private function getAvailableMovies($days){
        $cinemaUrl = $this->url . substr($this->startPageLinks["Stadens biograf!"], 1);
        $movieDays = $this->scrape->getMovieDays($this->scrape->curlRequest($cinemaUrl));
        $movies = $this->scrape->getMovies($this->scrape->curlRequest($cinemaUrl));

        $availableMovieOccasions = array();

        foreach($movieDays as $day){
            if(in_array($day, $days)){
                echo "<br/>";
                foreach($movies as $movie){
                    $json = $this->scrape->curlRequest($cinemaUrl . '/check?day=' . $day . '&movie=' . $movie);
                    $movieOccasions = json_decode($json, true);

                    foreach($movieOccasions as $movieOccasion){
                        if($movieOccasion['status'] == 1){
                            array_push($availableMovieOccasions, array('day' => $day, 'time' => $movieOccasion['time'], 'movie' => $movieOccasion['movie']));
                        }
                    }
                }
            }
        }

        /*
        foreach($availableMovieOccasions as $occasion){
            echo "Dag " . $this->days[$occasion['day']] . ": Filmen " . $this->movies[$occasion['movie']] . " klockan " . $occasion['time'] . "<br />";
        }
        */

        return $availableMovieOccasions;
    }

    private function getAvaliableTables(){

    }

    public function getResult(){
        return $this->result;
    }

}