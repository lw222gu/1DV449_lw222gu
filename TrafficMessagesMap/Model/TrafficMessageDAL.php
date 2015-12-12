<?php

namespace Model;

class TrafficMessageDAL {

  public function getJson(){

    if(time() - filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE) > 3600){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, \Settings::APP_REQUEST_URL_SR);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);

      $doc = fopen(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE, "w");
      fwrite($doc, $data);
      fclose($doc);
    }
  }

  public function getMessages(){

    /*Check if file has been updated over the last minute, (last hour while developing)
     *otherwise fetch new data from API
     */
    /*if(time() - filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE) > 3600){
      $this->getJson();
    }*/

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

    usort($messages, function($a, $b)
    {
        return strcmp($a->getDate(), $b->getDate());
    });

    $messages = array_reverse($messages);

    return $messages;
  }
}
