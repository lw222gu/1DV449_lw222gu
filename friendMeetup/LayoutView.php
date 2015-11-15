<?php

class LayoutView {

    public function renderHtmlOutput(){
        echo'<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Friend Meetup</title>
                    <link rel="stylesheet" href="css/style.css" />
                </head>
                <body>
                    <div id="content">
                        <main>
                            <h1>Meetup!</h1>
                            ' . $this->renderForm() . '
                            ' . $this->renderResult() . '
                        </main>
                    </div>
                 </body>
              </html>
            ';
    }

    private function renderForm(){
        return '
            <form method = "post">
                <label for="url">Ange url: </label>
                <input type="text" name="url" id="url" />
                <input type="submit" value="Starta" id="startButton" />
            </form>
            ';
    }

    private function renderResult(){
        if(isset($_POST["url"])){
            $url = $_POST["url"];
            $meetup = new Meetup($url);
            //$result = new Scrape($url);
            return $meetup->getResult();
        }
        return "";
    }

}