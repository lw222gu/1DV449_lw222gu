<?php

class LayoutView {

    public function renderHtmlOutput(){
        echo'<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Friend Meetup</title>
                    <!-- <link rel="stylesheet" href="content/css/style.css" /> -->
                </head>
                <body>
                    <div id="content">
                        <main>
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
                <input type="text" name="url" />
                <input type="submit" value="Starta" />
            </form>
            ';
    }

    private function renderResult(){
        if(isset($_POST["url"])){
            $url = $_POST["url"];
            $result = new ScrapeResult($url);
            return $result->getResult();
        }
        return "";
    }

}