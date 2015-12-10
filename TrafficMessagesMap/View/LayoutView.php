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
            </head>
            <body>
                <div class="row">
                    <div class="large-12 columns">
                        <h1>Välkommen till trafikkartan</h1>
                    </div>
                </div>
                <div class="row">
                  <div class="large-4 medium-6 small-12 columns">
                    <h2>Trafikmeddelanden</h2>
                    <ul>' . $this->renderMessages() . '</ul>
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
      $ret .= '<li>
                <h3>' . $message->getTitle() . '</h3>
                <p class="date">' . $message->getDate() . '</p>
                <p class="category">' . $message->getCategory() . '</p>
                <p>' . $message->getDescription() . '</p>
              </li>';
    }
    return $ret;
  }
}
