<?php

class Meetup {

    private $url;
    private $scrape;
    private $startPageLinks;
    private $days = array("01" => "fredag", "02" => "lördag", "03" => "söndag");
    private $movies = array("01" => "Söderkåkar", "02" => "Fabian Bom", "03" => "Pensionat Paradiset");

    private $availableMeetups;

    public function __construct($url){
        $this->url = $url;
        $this->scrape = new Scrape();

        //Get cURL request data and send it into getLinks
        $this->startPageLinks = $this->scrape->getLinks($this->scrape->curlRequest($url));

        $availableDays = $this->getAvailableCalendarDays();
        $availableMovieOccasions = $this->getAvailableMovies($availableDays);
        $this->availableMeetups = $this->getAvaliableTables($availableMovieOccasions, $availableDays);
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

    private function getAvailableMovies($days){
        $cinemaUrl = $this->url . substr($this->startPageLinks["Stadens biograf!"], 1);
        $movieDays = $this->scrape->getMovieDays($this->scrape->curlRequest($cinemaUrl));
        $movies = $this->scrape->getMovies($this->scrape->curlRequest($cinemaUrl));

        $availableMovieOccasions = array();

        foreach($movieDays as $day){
            if(in_array($day, $days)){
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

        return $availableMovieOccasions;
    }

    private function getAvaliableTables($availableMovieOccasions, $availableDays){
        $dinnerUrl = $this->url . substr($this->startPageLinks["Zekes restaurang!"], 1) . "/";
        $dinnerOccasions = $this->scrape->getDinnerOccasions($this->scrape->curlRequest($dinnerUrl));

        $dinners = array("01" => array(), "02" => array(), "03" => array());

        foreach($dinnerOccasions as $dinnerOccasion){
            if(strpos($dinnerOccasion, "fre") === 0 && in_array("01", $availableDays)){
                array_push($dinners["01"], str_replace("fre", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "lor") === 0 && in_array("02", $availableDays)){
                array_push($dinners["02"], str_replace("lor", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "son") === 0 && in_array("03", $availableDays)){
                array_push($dinners["03"], str_replace("son", "", $dinnerOccasion));
            }
        }

        $resultArray = array();

        foreach($availableMovieOccasions as $availableMovieOccasion){
            foreach($dinners as $dinner){
                foreach($dinner as $time){
                    $formattedTime = $time[0] . $time[1] . "-" . $time[2] . $time[3];
                    if(str_replace(":", "", $availableMovieOccasion["time"]) + 200 <= $time){
                        array_push($resultArray, "Ni kan se filmen " . $this->movies[$availableMovieOccasion["movie"]]. " på " . $this->days[$availableMovieOccasion["day"]] . " kl " . $availableMovieOccasion["time"] . " och äta middag kl " . $formattedTime . ".");
                    }
                }
            }
        }

        $resultList = "<ul>";

        foreach($resultArray as $result){
            $resultList .= "<li>" . $result . "</li>";
        }
        $resultList .= "</ul>";

        return $resultList;
    }

    public function getResult(){
        return $this->availableMeetups;
    }
}