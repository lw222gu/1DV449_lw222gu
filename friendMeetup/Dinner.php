<?php

class Dinner {

    private $scrape;

    public function __construct(){
        $this->scrape = new Scrape();
    }

    public function getAvaliableTables($day, $movieTime, $url){

        $movieTime = str_replace(":", "", $movieTime);
        $dinnerUrl = $url . $_SESSION['startPageLinks']["Zekes restaurang!"] . "/";
        session_unset("startPageLinks");

        $dinnerOccasions = $this->scrape->getDinnerOccasions($this->scrape->curlRequest($dinnerUrl));

        $dinners = array();

        foreach($dinnerOccasions as $dinnerOccasion){
            if(strpos($dinnerOccasion, "fre") === 0 && $day == "01" && str_replace("fre", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("fre", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "lor") === 0 && $day == "02" && str_replace("lor", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("lor", "", $dinnerOccasion));
            }
            if(strpos($dinnerOccasion, "son") === 0 && $day == "03" && str_replace("son", "", $dinnerOccasion) - 200 >= $movieTime){
                array_push($dinners, str_replace("son", "", $dinnerOccasion));
            }
        }

        return $dinners;
    }
}