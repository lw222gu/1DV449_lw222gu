<?php

namespace Model;

class TrafficMessage {

  private $title;
  private $date;
  private $category;
  private $description;

  public function __construct($title, $date, $category, $description){
    $this->title = $title;
    $this->date = $date;
    $this->category = $category;
    $this->description = $description;

    $this->setDate($date);
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDate(){
    return $this->date;
  }

  public function setDate($date){
    $dateTime = str_replace("/Date(", "", $date);
    $dateTime = str_replace(")/", "", $dateTime);
    $dateTime = substr($dateTime, 0, -5);
    $this->date = $dateTime;
  }


  public function getCategory(){
    return $this->category;
  }

  public function getDescription(){
    return $this->description;
  }
}
