<?php

class Scrape{

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

    public function getCalendar($data){
        $DOMTableDatas = $this->getXPath($data)->query('//td');
        $tableData = array();
        foreach($DOMTableDatas as $DOMTableData){
            if(strcasecmp($DOMTableData->nodeValue, "ok") == 0){
                array_push($tableData, 1);
            }
            else {
                array_push($tableData, 0);
            }
        }
        return $tableData;
    }

    public function getMovieDays($data){
        $DOMMovieDays = $this->getXPath($data)->query('//select[@id="day"]//option[not(@disabled)]');
        $days = array();
        foreach($DOMMovieDays as $DOMMovieDay){
            array_push($days, $DOMMovieDay->getAttribute("value"));
        }
        return $days;
    }

    public function getMovies($data){
        $DOMMovies = $this->getXPath($data)->query('//select[@id = "movie"]/option[not(@disabled)]');
        $movies = array();

        foreach($DOMMovies as $DOMMovie){
            $movies[$DOMMovie->nodeValue] = $DOMMovie->getAttribute("value");
            //array_push($movies, $DOMMovie->getAttribute("value"));
        }
        return $movies;
    }

    public function getDinnerOccasions($data){
        libxml_use_internal_errors(TRUE);
        $DOMDinnerOccasions = $this->getXPath($data)->query('//input[@type = "radio"]');
        $dinnerOccasions = array();
        foreach($DOMDinnerOccasions as $DOMDinnerOccasion){
            array_push($dinnerOccasions, $DOMDinnerOccasion->getAttribute("value"));
        }
        return @$dinnerOccasions;
    }
}