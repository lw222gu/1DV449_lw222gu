<?php

namespace Model;

class TrafficMessageDAL {

  public function getJson(){

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'http://api.sr.se/api/v2/traffic/messages?format=json&pagination=false&indent=true');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
  }

  public function saveJson($data){
    $json = json_decode($data, true);
    $messages = $json["messages"];

    /* Remove unneccesary characters in date string */
    for($i = 0; $i < count($messages); $i++){
      $date = $messages[$i]["createddate"];
      $date = str_replace("/Date(", "", $date);
      $date = substr($date, 0, 13);
      $messages[$i]["createddate"] = $date;
    }

    /* Sort messages by date */
    usort($messages, function($a, $b)
    {
        return strcmp($a["createddate"], $b["createddate"]);
    });
    $json["messages"] = array_reverse($messages);

    /* Write json to file */
    $doc = fopen('Resources/trafficMessages.json', "w");
    fwrite($doc, json_encode($json));
    fclose($doc);
  }
}
