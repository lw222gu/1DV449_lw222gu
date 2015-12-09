<?php

namespace Model;

class TrafficMessageDAL {

  public function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($data, true);
    return $json;
  }

  public function getMessages($url){
    $json = $this->getJson($url);
    $jsonMessages = $json["messages"];

    $messages = array();

    foreach($jsonMessages as $jsonMessage){
        $message = new TrafficMessage($jsonMessage["title"], $jsonMessage["createddate"], $jsonMessage["category"], $jsonMessage["description"]);
        array_push($messages, $message);
    }

    return $messages;
  }
}
