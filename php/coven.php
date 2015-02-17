<?php
include '../inc/functions.php';

$uuid = '123456789'; // test uuid

$action = $_GET['action'];

switch ($action) {

  case null:

	$players = array(); 

	$db->query('SELECT u.level, uc.pid FROM users u, users_coven uc WHERE u.id = uc.pid AND uc.uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$row = $db->resultset();
	
  $ar = array('players' => $row);

  break;

  case 'add-player':

	$code = $_GET['code']; settype($code, 'integer');
	$exists = false;

	$db->query("SELECT id FROM users WHERE id = :code");
	$db->bind(':code', $code);
	$player = $db->single();

	// Add a row if the player exists
	if (!empty($player)) {
		$exists = true;

		//TODO: Check if this player is not already in their coven. Do not add if so.

		$db->query("INSERT INTO users_coven (uuid, pid) VALUES (:uuid, :code)");
		$db->bind(':uuid', $uuid);
		$db->bind(':code', $code);
		$db->execute();
	}

	$ar = array('exists' => $exists);

	break;
}

echo json_encode($ar);