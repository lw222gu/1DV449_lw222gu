<?php

namespace View;

class LayoutView {

  public function renderPage(){
    echo'<!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <meta http-equiv="x-ua-compatible" content="ie=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Trafikkartan</title>
                <link rel="stylesheet" href="content/css/foundation.css" />
                <link rel="stylesheet" href="content/css/app.css" />
                <link rel="stylesheet" href="content/css/main.css" />
            </head>
            <body>
                <div id="topbar">
                  <h1>Trafikkartan</h1>
                </div>
                <div class="row">
                    <div class="large-12 columns">
                        <h1>Välkommen till trafikkartan</h1>
                    </div>
                </div>
                <div class="row">
                  <div class="large-4 medium-6 small-12 columns">
                    <h2>Trafikmeddelanden</h2>
                    <label for="select-category">Filtrera på kategori:</label>
                    <select id="select-category">' . $this->renderCategoryOptions() . '</select>
                    <ul class="messages-list">' . $this->renderMessages() . '</ul>
                  </div>
                </div>
            </body>
        </html>
        ';
  }

  private function renderMessages(){
    $ret = "";
    $dal = new \Model\TrafficMessageDAL();
    $messages = $dal->getMessages("http://api.sr.se/api/v2/traffic/messages?format=json&indent=true");

    foreach($messages as $message){
      $description = $message->getDescription();
      if($description == ""){
        $description = "Beskrivning saknas.";
      }
      $ret .= '<li class="' . \Settings::CSS_TRAFFIC_MESSAGE_CLASSES[$message->getCategory()] . '">
                <h3>' . $message->getTitle() . '</h3>
                <p class="date">' . $message->getDate() . ' | </p>
                <p class="category">' . $message->getCategory() . '</p>
                <p>' . $description . '</p>
              </li>';
    }

    return $ret;
  }

  private function renderCategoryOptions(){
    $ret = '<option value="Visa alla kategorier">Visa alla kategorier</option>';

    foreach (\Settings::APP_TRAFFIC_MESSAGE_CATEGORIES as $key => $category) {
      $ret .= '<option value="' . \Settings::APP_TRAFFIC_MESSAGE_CATEGORIES[$key] . '">' . \Settings::APP_TRAFFIC_MESSAGE_CATEGORIES[$key] . '</option>';
    }

    return $ret;
  }
}
