<?php

include_once '../../classes/db.php';
include_once '../../classes/user.php';

$db = new Db();
$user = new User();
$user->setDb($db);

$user = $user->getStats();

$ar = array(
	'user' => $user
);

echo json_encode($ar);