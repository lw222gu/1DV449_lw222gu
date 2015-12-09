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
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDate(){
    return $this->date;
  }

  public function getCategory(){
    return $this->category;
  }

  public function getDescription(){
    return $this->description;
  }
}
