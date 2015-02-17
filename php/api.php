<?php
header('Access-Control-Allow-Origin: *');
include 'inc/functions.php';

$uuid = '123456789'; // test uuid
//$uuid = $_GET['uuid'];

$action = $_GET['action'];

switch ($action) {

	case 'get-stats':

	$newplayer = false;

	$db->query('SELECT id FROM users WHERE uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$deviceexists = $db->rowCount();

	if ($deviceexists == 0) {
		$newplayer = true;

		$covencode = mt_rand(111111,999999);

		$db->query('INSERT INTO users (uuid, coven_code) VALUES (:uuid, :covencode)');
		$db->bind(':uuid', $uuid);
		$db->bind(':covencode', $covencode);
		$db->execute();

		$db->query('INSERT INTO users_missions (uuid) VALUES (:uuid)');
		$db->bind(':uuid', $uuid);
		$db->execute();

		$db->query('INSERT INTO users_abilities (uuid) VALUES (:uuid)');
		$db->bind(':uuid', $uuid);
		$db->execute();
	}

	// now select their row
	$db->query('SELECT level, exp, blood, energyMax, energyMaxedAt, atk, def FROM users WHERE uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$user = $db->single();

	$user['explvl'] = expLevelLimit($user['level']); 

	// At this point, let's assume the user has full energy
	$user['secondsToMaxEnergy'] = 0;

	if ($user['energyMaxedAt'] < $tstamp) {
		$user['energy'] = $user['energyMax'];
	}
	else {
		$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
		$user['energy'] = calculateRemainingEnergy($user['secondsToMaxEnergy'], $user['energyMax']);
	}
	
	$ar = array('user' => $user, 'newplayer' => $newplayer, 'count' => $count);

	break;

	case 'page-stats':
	{

		$db->query('SELECT atk, def, sp, battlesWon FROM users WHERE uuid = :uuid');
		$db->bind(':uuid', $uuid);
		$user = $db->single();
		
	  $ar = array('user' => $user);

	  break;
	}

	case 'get-skills':

	// get # of skill points
	$db->query('SELECT atk, def, energyMax, sp FROM users WHERE uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$user = $db->single();
	
 	$ar = array('user' => $user);

	break;

	case 'upgrade-skill':

	$id = $_GET['id']; settype($id, 'integer');

	// get current skill points
	$db->query('SELECT sp, energyMaxedAt FROM users WHERE uuid = :uuid');
	$db->bind(':uuid', $uuid);
	$user = $db->single();


	if ($user['sp'] > 0) {
		$upgraded = true;
		$user['sp'] -= 1;

		switch($id) {
			case 1: $field = 'atk'; $skillname = 'Attack'; break;
			case 2: $field = 'def'; $skillname = 'Defence'; break;
			case 3: $field = 'energyMax'; $skillname = 'Max Energy'; break;
		}

		if ($id == 3) {
			if ($user['energyMaxedAt'] > $tstamp) {
				$user['energyMaxedAt'] += ENERGY_WAIT_PER_INCREASE;
			}
			else {
				$user['energyMaxedAt'] = $tstamp + ENERGY_WAIT_PER_INCREASE;
			}

			$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
		}

		$db->query("UPDATE users SET {$field} = {$field} + 1, 
																		sp = {$user['sp']},
																		energyMaxedAt = {$user['energyMaxedAt']}
																		WHERE uuid = :uuid");
		$db->bind(':uuid', $uuid);
		$db->execute();
	}
	
  $ar = array('user' => $user, 'upgraded' => $upgraded, 'sp' => $user['sp'], 'skillname' => $skillname, 'elemId' => $field);

	break;

	case 'get-shrine-data':

  $packs = array(array('Pouch of 50', 4.99), 
  							 array('Bag of 110', 9.99),
  							 array('Sack of 230', 19.99), 
  							 array('Coffin of 400', 34.99), 
  							 array('Crypt of 850', 69.99));

  $upgrades = array(array('Energy Refill', 10), 
  							 array('+5% EXP Boost', 30),
  							 array('+5% Blood Boost', 30), 
  							 array('+1 Coven Member', 20));

	$ar = array('packs' => $packs, 'upgrades' => $upgrades);

	break;

	case 'buy-upgrade':
	{
		$id = $_GET['id'];

		switch ($id) {
			case 1: $upgradename = 'Energy Refill'; break;
			case 2: $upgradename = '+5% EXP Boost'; break;
			case 3: $upgradename = '+5% Blood Boost'; break;
			case 4: $upgradename = '+1 Coven Member'; break;
		}

		$ar = array('upgradename' => $upgradename);
		break;
	}
}

echo json_encode($ar);