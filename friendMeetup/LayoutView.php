<?php

class LayoutView {

    private $meetup;

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
        if(!isset($_GET["day"])){
            return '
                <form method = "post">
                    <label for="url">Ange url: </label>
                    <input type="text" name="url" id="url" />
                    <input type="submit" value="Starta" id="startButton" />
                </form>
                ';
        }
        return "";
    }

    private function renderResult(){

        if(isset($_POST["url"])){
            $_SESSION["url"] = $_POST["url"];
            $this->meetup = new Meetup($_SESSION["url"]);
            return $this->renderMovieList();
        }

        if(isset($_GET["day"])){
            $this->meetup = new Meetup($_SESSION["url"]);
            return $this->renderTablesList();
        }

        return "";
    }

    private function renderMovieList(){

        $movieOccasions = $this->meetup->getAvailableMovies();

        $ret = "<h2>Följande filmer hittades</h2>";

        if(count($movieOccasions) != 0){
            $ret .= "<ul>";
            foreach($movieOccasions as $movieOccasion){
                $ret .= "<li>Ni kan se filmen " . $movieOccasion["movie"] . " på " . $movieOccasion["day"] . " kl. " . $movieOccasion["time"] . ".
                <br /><a href='?day=" . $movieOccasion['dayCode'] . "&time=" . $movieOccasion['time'] . "&movie=" . $movieOccasion['movie'] . "'>Välj denna och boka bord</a></li>";
            }
            $ret .= "</ul>";
        }

        else {
            $ret .= "<p>Inga lediga filmer hittades.</p>";
        }

        return $ret;
    }

    private function renderTablesList(){
        $tables = $this->meetup->getAvaliableTables($_GET["day"], $_GET["time"]);

        $ret = "<h2>Följande lediga bord finns</h2>";

        if(count($tables) != 0){
            $ret .= "<ul>";
            foreach($tables as $table){
                $ret .= "<li>Ni kan se filmen " . $_GET["movie"] . " kl " . $_GET["time"] . " och äta på Zekes Restaurang kl " . $table[0] . $table[1] . "-" . $table[2] . $table[3] . ".</li>";
            }
            $ret .= "</ul>";
        }

        else {
            $ret .= "<p>Inga lediga bord hittades.</p>";
        }

        return $ret;
    }

}