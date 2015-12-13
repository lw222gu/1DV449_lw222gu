<?php

namespace Model;

class TrafficMessageDAL {

  private $url = "http://api.sr.se/api/v2/traffic/messages?format=json&pagination=false&indent=true";
  private $jsonUrl = "Resources\TrafficMessages.json";


  public function getJson(){

    /*Check if file has been updated over the last minute, (last hour while developing)
     *otherwise fetch new data from API
     */
    if(time() - filemtime($this->jsonUrl) > 3600){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);

      $json = json_decode($data, true);

      for($i = 0; $i < count($json["messages"]); $i++){
        $date = $json["messages"][$i]["createddate"];
        $date = str_replace("/Date(", "", $date);
        $date = substr($date, 0, 13);
        $json["messages"][$i]["createddate"] = $date;
      }

      //Fundera på att ta bort de fält som inte används redan innan filen sparas!

      $messages = $json["messages"];
      usort($messages, function($a, $b)
      {
          return strcmp($a["createddate"], $b["createddate"]);
      });
      $json["messages"] = array_reverse($messages);

      $doc = fopen($this->jsonUrl, "w");
      fwrite($doc, json_encode($json));
      fclose($doc);
    }
  }
/*
  public function getMessages(){

    $str = file_get_contents(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE);
    $json = json_decode($str, true);
    $jsonMessages = $json["messages"];

    $messages = array();

    foreach($jsonMessages as $jsonMessage){
        $message = new TrafficMessage(
                                      $jsonMessage["title"],
                                      $jsonMessage["createddate"],
                                      $jsonMessage["category"],
                                      $jsonMessage["description"],
                                      $jsonMessage["latitude"],
                                      $jsonMessage["longitude"]
                                    );
        array_push($messages, $message);
    }

    return $messages;
  }*/
}
