<?php

require_once('LayoutView.php');
require_once('Meetup.php');
require_once('Dinner.php');
require_once('Scrape.php');

session_start();

//MAKE SURE ERRORS ARE SHOWN
/*error_reporting(E_ALL);
ini_set('display_errors', 'On');*/

$lv = new LayoutView();
$lv->renderHtmlOutput();