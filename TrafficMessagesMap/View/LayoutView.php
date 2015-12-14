<?php

namespace View;

class LayoutView {

  private $dal;

  public function __construct(){
    $this->dal = new \Model\TrafficMessageDAL();
    $this->dal->getJson();
  }

  public function renderPage(){
    echo'<!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <meta http-equiv="x-ua-compatible" content="ie=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Trafikkartan</title>
                <link rel="stylesheet" href="Content/css/foundation.css" />
                <link rel="stylesheet" href="Content/css/main.css" />
                <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
                <script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
            </head>
            <body>
                <div id="topbar">
                  <h1>Trafikkartan</h1>
                </div>
                <!--<div class="row">
                    <div class="large-12 medium-12 small-12 columns">
                        <h1>Välkommen till trafikkartan</h1>
                    </div>
                </div>-->

                <!--<div class="row">
                <div class="large-12 medium-12 small-12 columns" id="map">-->
                  <div class="large-12 medium-12 small-12 columns" id="map">
                    <noscript><p class="no-js">Du har javascript avstängt och kan därför inte se kartan. <br/> Slå gärna på javascript i din webbläsare för att ta del av hela sidans innehåll.</p></noscript>
                  </div>
                <!--</div>-->
                <div class="row">
                  <div class="large-4 medium-12 small-12 columns">
                    <h2>Trafikmeddelanden</h2>
                  </div>
                    <div class="large-6 medium-12 small-12 columns right">
                      <ul class="color-definitions">
                        <li class="prio1">Mycket allvarlig händelse</li>
                        <li class="prio2">Stor händelse</li>
                        <li class="prio3">Störning</li>
                        <li class="prio4">Information</li>
                        <li class="prio5">Mindre störning</li>
                      </ul>
                    </div>
                </div>

                <div class="row">
                  <div class="large-12 medium-12 small-12 columns">
                    <select id="select-category">
                      <option value="0">Vägtrafik</option>
                      <option value="1">Kollektivtrafik</option>
                      <option value="2">Planerad störning</option>
                      <option value="3">Övrigt</option>
                      <option selected="selected" value="4">Visa alla kategorier</option>
                    </select>
                  </div>
                </div>

                <noscript>
                  <div class="row">
                    <div id="noscript-traffic-messages" class="large-12 medium-12 small-12 columns">
                        ' . //$this->renderMessages() .
                        '
                    </div>
                  <div>
                </noscript>

                <div class="row">
                  <div id="traffic-messages" class="large-12 medium-12 small-12 columns">
                  </div>
                </div>
                <script src="Content/js/app.js"></script>
            </body>
        </html>
        ';
  }
/*
  private function renderMessages(){
    $ret = "";

    $messages = $this->dal->getMessages();

    date_default_timezone_set('Europe/Stockholm');
    $timeStamp = date('Y-m-d, H.i.s', filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE));

    if($messages != null){
      //Ska nog inte använda timestampet så här, utan bara ha det med när det inte går att läsa in data.
      //$ret .= '<p class="timestamp">Informationen hämtades senast: ' . $timeStamp . '</p><ul class="messages-list">';
      $ret .= '<ul class="messages-list">';

      foreach($messages as $message){
        $description = $message->getDescription();

        if($description == ""){
          $description = "Beskrivning saknas.";
        }

        $ret .= '<li>
                  <h3>' . $message->getTitle() . '</h3>
                  <p class="date">' . date('Y-m-d H.i.s', $message->getDate()) . '</p>
                  <p class="category">' . $message->getCategory() . '</p>
                  <p>' . $description . '</p>
                </li>';
      }
      $ret .= '</ul>';
    }

    return $ret;
  }*/
}
