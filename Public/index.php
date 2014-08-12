<?php
// Include St. Elmo's Fire
require_once '../elmo.php';

// Create a new instance of elmo
$elmo = new Elmo();

// Render elmo to string
$elmo->render()->out();