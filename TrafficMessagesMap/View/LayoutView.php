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
          <link rel="stylesheet" href="Content/css/foundation.css" />
          <link rel="stylesheet" href="Content/css/main.css" />
          <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
        </head>

        <body>
          <div id="topbar">
            <h1>Trafikkartan</h1>
            <div class="color-definitions">
              <ul>
                <li class="prio1">Mycket allvarlig händelse</li>
                <li class="prio2">Stor händelse</li>
                <li class="prio3">Störning</li>
                <li class="prio4">Information</li>
                <li class="prio5">Mindre störning</li>
              </ul>
            </div>
          </div>

          <noscript>
            <p class="no-js">Du har javascript avstängt och kan därför inte se innehållet på sidan. <br/>
              Slå gärna på javascript i din webbläsare för att ta del av sidans innehåll.
            </p>
          </noscript>

          <div class="large-12 medium-12 small-12 columns" id="map"></div>
          <div class="row">
            <div class="large-4 medium-12 small-12 columns">
              <h2>Trafikmeddelanden</h2>
            </div>
          </div>

          <div class="row">
            <div class="large-12 medium-12 small-12 columns">
              <p class="filter">Filter:</p>
              <a href="#" class="category-a" value="4"><img src="Content/css/images/markers-sprite.svg#svgView(viewBox(0, 800, 31, 35))" class="category-img" alt="Visa alla kategorier"/></a>
              <a href="#" class="category-a" value="0"><img src="Content/css/images/markers-sprite.svg#svgView(viewBox(0, 840, 31, 35))" class="category-img" alt="Visa vägtrafik"/></a>
              <a href="#" class="category-a" value="1"><img src="Content/css/images/markers-sprite.svg#svgView(viewBox(0, 880, 31, 35))" class="category-img" alt="Visa kollektivtrafik"/></a>
              <a href="#" class="category-a" value="2"><img src="Content/css/images/markers-sprite.svg#svgView(viewBox(0, 920, 31, 35))" class="category-img" alt="Visa planerade störningar"/></a>
              <a href="#" class="category-a" value="3"><img src="Content/css/images/markers-sprite.svg#svgView(viewBox(0, 960, 31, 35))" class="category-img" alt="Visa övrigt"/></a>
            </div>
          </div>

          <div class="row">
            <div id="traffic-messages" class="large-12 medium-12 small-12 columns">
              <p>' //. $this->getLastModifiedTime()
               . '</p>
            </div>
          </div>
          <script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
          <script src="Content/js/app.js"></script>
        </body>
      </html>';
  }

  private function getLastModifiedTime(){
    date_default_timezone_set('Europe/Stockholm');
    $timeStamp = date('Y-m-d, H.i.s', filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE));
    return 'Informationen uppdaterades senast ' . $timeStamp;
  }
}
