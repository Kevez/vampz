<?php

include_once '../../classes/db.php';

$db = new Db();

$ar = array(
	'level' => 1
);

echo json_encode($ar);