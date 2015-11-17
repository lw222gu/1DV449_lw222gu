<?php

class Meetup {

    private $url;
    private $scrape;
    private $startPageLinks;
    private $days = array("01" => "fredag", "02" => "lördag", "03" => "söndag");

    private $availableDays;

    public function __construct($url){
        $this->url = $url;
        $this->scrape = new Scrape();

        //Get cURL request data and send it into getLinks
        $this->startPageLinks = $this->scrape->getLinks($this->scrape->curlRequest($url));
        $_SESSION['startPageLinks'] = $this->startPageLinks;
    }

    private function getAvailableCalendarDays(){
        $calendarUrl = $this->url . $this->startPageLinks["Kalendrar"] . "/";
        $calendarLinks = $this->scrape->getLinks($this->scrape->curlRequest($calendarUrl));

        $allCalendars = array();

        //Adds every persons calendar link to one common array ($allCalendars).
        foreach($calendarLinks as $link){
            $personalCalendar = $this->scrape->getCalendar($this->scrape->curlRequest($calendarUrl . "/" . $link));
            array_push($allCalendars, $personalCalendar);
        }

        $days = array(0, 0, 0);

        //Adds 1 to each day in $days array, for every person that is available that day.
        foreach($allCalendars as $calendar){
            for($i = 0; $i < count($calendar); $i = $i+1){
                $days[$i] += $calendar[$i];
            }
        }

        $availableDays = array();

        /*
         * Checks if the total number of available persons for each day is equal to each other,
         * and if that´s the case, adds that day to $availableDays array.
         */
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
        $availableMovieOccasions = array();

        if(count($this->availableDays) == 0){
            $availableMovieOccasions["error"] = "Ingen dag finns där alla är lediga.";
        }

        else {
            $cinemaUrl = $this->url . $this->startPageLinks["Stadens biograf!"]  . "/";
            $movieDays = $this->scrape->getMovieDays($this->scrape->curlRequest($cinemaUrl));
            $movies = $this->scrape->getMovies($this->scrape->curlRequest($cinemaUrl));

            foreach($movieDays as $day){
                if(in_array($day, $this->availableDays)){
                    foreach($movies as $movie){
                        $json = $this->scrape->curlRequest($cinemaUrl . '/check?day=' . $day . '&movie=' . $movie);
                        $movieOccasions = json_decode($json, true);

                        foreach($movieOccasions as $movieOccasion){
                            if($movieOccasion['status'] == 1){
                                array_push($availableMovieOccasions, array(
                                    'dayCode' => $day,
                                    'day' => $this->days[$day],
                                    'time' => $movieOccasion['time'],
                                    'movie' => array_search((string)$movieOccasion['movie'], $movies)
                                ));
                            }
                        }
                    }
                }
            }
        }

        return $availableMovieOccasions;
    }
}