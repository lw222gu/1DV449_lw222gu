<?php
require_once('Model/TrafficMessageDAL.php');
require_once('View/LayoutView.php');
require_once('settings.php');

//MAKE SURE ERRORS ARE SHOWN
error_reporting(E_ALL);
ini_set('display_errors', 'On');



/*Check if file has been updated over the last minute, (last hour while developing)
 *otherwise fetch new data from API
 */
if(time() - filemtime(\Settings::APP_TRAFFIC_MESSAGES_JSON_FILE) > 3600){
  $dal = new \Model\TrafficMessageDAL();
  $dal->getJson();
}

$lv = new View\LayoutView();
$lv->renderPage();
