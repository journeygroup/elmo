<?php
// Start the autoloader
require_once "../vendor/autoload.php";

// Create a new instance of elmo
$elmo = new Journey\Elmo();

// Render elmo to string
$elmo->render()->out();
