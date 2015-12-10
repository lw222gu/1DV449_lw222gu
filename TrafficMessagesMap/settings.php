<?php

class Settings {

    //TRAFFIC MESSAGE CATEGORIES:
    const APP_TRAFFIC_MESSAGE_CATEGORIES = array(
      "0" => "Vägtrafik",
      "1" => "Kollektivtrafik",
      "2" => "Planerad störning",
      "3" => "Övrigt"
    );

    //TRAFFIC MESSAGE CLASSES
    const CSS_TRAFFIC_MESSAGE_CLASSES = array(
      "Vägtrafik" => "vagtrafik",
      "Kollektivtrafik" => "kollektivtrafik",
      "Planerad störning" => "planerad-storning",
      "Övrigt" => "ovrigt"
    );

    //PATH TO CACHED FILE WITH TRAFFIC messages
    const APP_TRAFFIC_MESSAGES_JSON_FILE = "Resources\TrafficMessages.json";
}
