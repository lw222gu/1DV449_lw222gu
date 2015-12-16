<?php
require_once('Model/TrafficMessageDAL.php');
require_once('View/LayoutView.php');

//MAKE SURE ERRORS ARE SHOWN
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

$timeStamp = "";
$jsonUrl = "Resources/trafficMessages.json";

/*Check if file has been updated over the last two minutes,
 *otherwise fetch new data from API
 */
if(time() - filemtime($jsonUrl) > 120){
  $dal = new Model\TrafficMessageDAL();
  $data = $dal->getJson();

  if($data === null){

    $timeStamp = '<p class="timestamp">Ooops! Ingen data kunde hämtas. Informationen som visas hämtades ' .
                  date('Y-m-d, H:i:s', filemtime($jsonUrl)) . '.</p>';
  }

  else {
    $dal->saveJson($data);
  }
}

$lv = new View\LayoutView();
$lv->renderPage($timeStamp);
