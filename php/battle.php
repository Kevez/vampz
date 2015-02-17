<?php
include '../inc/functions.php';

$uuid = '123456789'; // test uuid

$action = $_GET['action'];

switch ($action) {

	case 'get-attackable-players':

	// pull out some players
	$db->query("SELECT id, level FROM users WHERE uuid != '{$uuid}' ORDER BY level DESC LIMIT 5");
	$db->bind(':uuid', $uuid);
	$players = $db->resultset();

  $ar = array('players' => $players);

	break;

	case 'battle-player':

	$id = $_GET['id'];
	$battle = array();
	$error = false;
	$energyused = 1;

	$db->query('SELECT level, exp, blood, energyMax, energyMaxedAt FROM users WHERE uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$user = $db->single();

	// check if player code exists
	//$chkcode = mysql_result(mysql_query("SELECT COUNT(id) FROM users WHERE code = {$code}"));

	if ($user['energyMaxedAt'] > $tstamp) {
		$user['energyMaxedAt'] += ENERGY_WAIT_PER_INCREASE * $energyused;
	}
	else {
		$user['energyMaxedAt'] = $tstamp + (ENERGY_WAIT_PER_INCREASE * $energyused);
	}

	$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
	$user['energy'] = calculateRemainingEnergy($user['secondsToMaxEnergy'], $user['energyMax']);

	// check to see if player has levelled up
	if ($user['energy'] < 0) {
		$error = true;
	}
	else {

		$expgain = 1;
		$bloodgain = mt_rand(20,50);

		$battle['id'] = $id;
		$battle['expgain'] = $expgain;
		$battle['bloodgain'] = $bloodgain;

		$user['exp'] += $expgain;
		$user['explvl'] = expLevelLimit($user['level']); 
		$user['blood'] += $bloodgain;

		if ($user['exp'] >= expLevelLimit($user['level'])) {
			$user['exp'] = $user['exp'] - expLevelLimit($user['level']);
			$user['level'] += 1;
			$user['explvl'] = expLevelLimit($user['level']);

			// replenish player's energy
			$user['energyMaxedAt'] = $tstamp - 1;
			$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
			$user['energy'] = $user['energyMax'];
			$user['levelUp'] = true;

			$db->query("UPDATE users SET level = {$user['level']},
																		  exp = {$user['exp']},
																		  blood = {$user['blood']},
																		  energyMaxedAt = {$user['energyMaxedAt']},
																		  sp = sp + 3
															  		  WHERE uuid = (:uuid)");
			$db->bind(':uuid', $uuid);
			$db->execute();
		}

		$db->query("UPDATE users SET exp = {$user['exp']},
																		blood = {$user['blood']},
																		energyMaxedAt = {$user['energyMaxedAt']},
																		battlesWon = battlesWon + 1
														  		  WHERE uuid = (:uuid)");
		$db->bind(':uuid', $uuid);
		$db->execute();

	}

	$ar = array('battle' => $battle, 'user' => $user, 'error' => $error);

	break;
}

echo json_encode($ar);