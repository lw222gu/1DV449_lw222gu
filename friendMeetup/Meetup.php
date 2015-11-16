<?php

class Meetup {

    private $url;
    private $scrape;
    private $startPageLinks;
    private $days = array("01" => "fredag", "02" => "lördag", "03" => "söndag");
    private $movies = array("01" => "Söderkåkar", "02" => "Fabian Bom", "03" => "Pensionat Paradiset");

    private $availableDays;
    private $availableMovieOccasions;

    public function __construct($url){
        $this->url = $url;

        $this->scrape = new Scrape();

        if($this->url != null){
            //Get cURL request data and send it into getLinks
            $this->startPageLinks = $this->scrape->getLinks($this->scrape->curlRequest($url));
            $_SESSION['startPageLinks'] = $this->startPageLinks;
        }
    }

    private function getAvailableCalendarDays(){
        $calendarUrl = $this->url . $this->startPageLinks["Kalendrar"] . "/";
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
            array_push($availableDays, "01");
        }
        if($days[1] == count($allCalendars)){
            array_push($availableDays, "02");
        }
        if($days[2] == count($allCalendars)){
            array_push($availableDays, "03");
        }

        return $availableDays;
    }

    public function getAvailableMovies(){

        $this->availableDays = $this->getAvailableCalendarDays();

        $cinemaUrl = $this->url . $this->startPageLinks["Stadens biograf!"];
        $movieDays = $this->scrape->getMovieDays($this->scrape->curlRequest($cinemaUrl));
        $movies = $this->scrape->getMovies($this->scrape->curlRequest($cinemaUrl));

        $this->availableMovieOccasions = array();

        foreach($movieDays as $day){
            if(in_array($day, $this->availableDays)){
                foreach($movies as $movie){
                    $json = $this->scrape->curlRequest($cinemaUrl . '/check?day=' . $day . '&movie=' . $movie);
                    $movieOccasions = json_decode($json, true);

                    foreach($movieOccasions as $movieOccasion){
                        if($movieOccasion['status'] == 1){
                            array_push($this->availableMovieOccasions, array('dayCode' => $day, 'day' => $this->days[$day], 'time' => $movieOccasion['time'], 'movie' => $this->movies[(string)$movieOccasion['movie']]));
                        }
                    }
                }
            }
        }

        return $this->availableMovieOccasions;
    }

    public function getAvaliableTables($availableDays, $movieTime){

        $movieTime = str_replace(":", "", $movieTime);
        $dinnerUrl = $this->url . $_SESSION['startPageLinks']["Zekes restaurang!"] . "/";

        $dinnerOccasions = $this->scrape->getDinnerOccasions($this->scrape->curlRequest($dinnerUrl));

        $dinners = array();

        foreach($dinnerOccasions as $dinnerOccasion){
            if(strpos($dinnerOccasion, "fre") === 0 && $availableDays == "01" && str_replace("fre", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("fre", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "lor") === 0 && $availableDays == "02" && str_replace("lor", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("lor", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "son") === 0 && $availableDays == "03" && str_replace("son", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("son", "", $dinnerOccasion));
            }
        }

        return $dinners;
    }
}