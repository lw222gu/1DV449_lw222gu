<?php
require_once('Model/TrafficMessageDAL.php');
require_once('View/LayoutView.php');
require_once('settings.php');

//MAKE SURE ERRORS ARE SHOWN
error_reporting(E_ALL);
ini_set('display_errors', 'On');


/*Check if file has been updated over the last five minutes,
 *otherwise fetch new data from API
 */
$timeStamp = "";

if(time() - filemtime(Settings::APP_TRAFFIC_MESSAGES_JSON_FILE) > 300){
  $dal = new Model\TrafficMessageDAL();
  $data = $dal->getJson();

  if($data === null){
    date_default_timezone_set('Europe/Stockholm');
    $timeStamp = '<p class="timestamp">Ooops! Ingen data kunde hämtas. Informationen som visas hämtades ' .
                  date('Y-m-d, H:i:s', filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE)) . '.</p>';
  }

  else {
    $dal->saveJson($data);
  }
}

$lv = new View\LayoutView();
$lv->renderPage($timeStamp);
