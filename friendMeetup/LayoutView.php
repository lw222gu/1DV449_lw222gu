<?php

class LayoutView {

    public function renderHtmlOutput(){
        echo'<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <!-- Tell browser not to cache data -->
                    <meta http-equiv="Cache-control" content="no-store">
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
            $meetup = new Meetup($_POST["url"]);
            return $this->renderMovieList($meetup);
        }

        if(isset($_GET["day"])){
            $dinner = new Dinner();
            return $this->renderDinnerTablesList($dinner);
        }

        return "";
    }

    private function renderMovieList($meetup){

        $movieOccasions = $meetup->getAvailableMovies();

        $ret = "<h2>Följande möjligheter till att träffas hittades</h2>";

        if(array_key_exists("error", $movieOccasions)){
            $ret .= "<p>" . $movieOccasions["error"] . "</p>";
        }

        else {
            if(count($movieOccasions) != 0){
                $ret .= "<ul>";
                foreach($movieOccasions as $movieOccasion){
                    $ret .= "
                        <li>
                            Ni kan se filmen " . $movieOccasion["movie"] .
                            " på " . $movieOccasion["day"] . " kl. " . $movieOccasion["time"] .
                            ".<br /><a href='?day=" . $movieOccasion['dayCode'] . "&time=" .
                            $movieOccasion['time'] . "&movie=" . $movieOccasion['movie'] .
                            "&url=" . $_POST["url"] . "'>Välj denna och boka bord</a>
                        </li>";
                }
                $ret .= "</ul>";
            }

            else {
                $ret .= "<p>Inga lediga filmer hittades.</p>";
            }
        }

        return $ret;
    }

    private function renderDinnerTablesList($dinner){
        $tables = $dinner->getAvaliableTables($_GET["day"], $_GET["time"], $_GET["url"]);
        $ret = "<h2>Följande lediga bord finns</h2>";

        if(count($tables) != 0){
            $ret .= "<ul>";
            foreach($tables as $table){
                $ret .= "
                        <li>
                            Ni kan se filmen " . $_GET["movie"] . " kl " . $_GET["time"] .
                            " och äta på Zekes Restaurang kl " . $table[0] . $table[1] .
                            "-" . $table[2] . $table[3] . ".
                        </li>";
            }
            $ret .= "</ul>";
        }

        else {
            $ret .= "<p>Inga lediga bord hittades.</p>";
        }

        return $ret;
    }
}