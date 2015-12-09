<?php
require_once 'Model\TrafficMessageDAL.php';
require_once 'Model\TrafficMessage.php';
require_once 'View\LayoutView.php';

//MAKE SURE ERRORS ARE SHOWN
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$lv = new View\LayoutView();
$lv->renderPage();
