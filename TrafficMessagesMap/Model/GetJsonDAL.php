<?php

namespace Model;

class GetJsonDAL {

  public function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($data, true);

    return $json;
  }
}
