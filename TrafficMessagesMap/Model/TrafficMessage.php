<?php

/*namespace Model;

class TrafficMessage {

  private $title;
  private $date;
  private $category;
  private $description;
  private $latitude;
  private $longitude;

  public function __construct($title, $date, $category, $description, $latitude, $longitude){
    $this->title = $title;
    $this->setDate($date);
    $this->setCategory($category);
    $this->description = $description;
    $this->latitude = $latitude;
    $this->longitude = $longitude;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDate(){
    return $this->date;
  }

  public function setDate($date){
    $dateTime = substr($date, 0, 10);
    $this->date = $dateTime;
  }

  public function getCategory(){
    return $this->category;
  }

  public function setCategory($category){
    $this->category = \Settings::APP_TRAFFIC_MESSAGE_CATEGORIES[$category];
  }

  public function getDescription(){
    return $this->description;
  }

  public function getLatitude(){
    return $this->latitude;
  }

  public function getLongitude(){
    return $this->longitude;
  }
}*/
