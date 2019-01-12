<?php
require_once "classes/controller.php";
header(Controller::$TYPE_JSON);
$ctrl = ["Bookings","Institution","User","Med_history","Med_prof","Survey"];

foreach ($ctrl as $take)
{
	require_once "./controllers/$take.php";
}
require_once "classes/app.php";
require_once "classes/Q_ueryBuild.php";
