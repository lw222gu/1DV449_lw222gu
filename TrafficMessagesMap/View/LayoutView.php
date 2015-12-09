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
            </body>
        </html>
        ';
  }
}
