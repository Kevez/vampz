<?php
include '../inc/functions.php';

define(MAX_MASTERY, 100);
define(MASTERY_INCREASE_PER_MISSION, 25);

$uuid = '123456789'; // test uuid

$action = $_GET['action'];

switch ($action) {

	case null:
	{
		$area =  $_GET['area'];
		$missionData = missionData();
		$offset = ($area - 1) * 10;
		$missionData = array_slice($missionData, $offset, 10);
		$fields = '';

		for ($i = ($offset + 1); $i < ($offset + 11); $i++) {
			$fields .= 'um.m'.$i.', ';
		}

		$fields = substr($fields, 0, -2);

		// get player's level and mission progress for this area
		$db->query("SELECT u.level, {$fields} FROM bl_users u, bl_users_missions um WHERE u.uuid = um.uuid AND u.uuid = :uuid");
		$db->bind(':uuid', $uuid);
		$user = $db->single();

		$ar = array('user' => $user, 'missionData' => $missionData, 'area' => $area);

		break;
	}
	case 'do-mission':
	{

		$id = $_GET['missionid'] - 1;
		$missions = missionData();
		$missionEXPAndRequiredEnergy = $id + 1;
		$expgain = $missionEXPAndRequiredEnergy;
		$bloodgain = mt_rand($missions[$id][1], floor($missions[$id][1]*1.1));

		$mission = array();

		// select player details
		$db->query('SELECT level, exp, blood, energyMax, energyMaxedAt, areasUnlocked FROM bl_users WHERE uuid = :uuid');
		$db->bind(':uuid', $uuid);
		$user = $db->single();

		if ($user['energyMaxedAt'] > $tstamp) {
			$user['energyMaxedAt'] += ENERGY_WAIT_PER_INCREASE * $missionEXPAndRequiredEnergy;
		}
		else {
			$user['energyMaxedAt'] = $tstamp + (ENERGY_WAIT_PER_INCREASE * $missionEXPAndRequiredEnergy);
		}

		$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
		$user['energy'] = calculateRemainingEnergy($user['secondsToMaxEnergy'], $user['energyMax']);

		if ($user['energy'] < 0) {
			$ar = array('error' => true);
		}
		else {

			$user['exp'] += $expgain;
			$user['blood'] += $bloodgain;
			$user['explvl'] = expLevelLimit($user['level']); 

			$mission['id'] = $id + 1;
			$mission['title'] = $missions[$id][0];
			$mission['expgain'] = $expgain;
			$mission['bloodgain'] = $bloodgain;
			$mission['energyused'] = $missionEXPAndRequiredEnergy;

			// check to see if player has levelled up
			if ($user['exp'] >= expLevelLimit($user['level'])) {
				$user['exp'] = $user['exp'] - expLevelLimit($user['level']);
				$user['level'] += 1;
				$user['explvl'] = expLevelLimit($user['level']);

				// replenish player's energy
				$user['energyMaxedAt'] = $tstamp - 1;
				$user['secondsToMaxEnergy'] = $user['energyMaxedAt'] - $tstamp;
				$user['energy'] = $user['energyMax'];
				$user['levelUp'] = true;

				$db->query("UPDATE bl_users SET level = {$user['level']},
																			  exp = {$user['exp']},
																			  blood = {$user['blood']},
																			  energyMaxedAt = {$user['energyMaxedAt']},
																			  sp = sp + 3
																  		  WHERE uuid = (:uuid)");
				$db->bind(':uuid', $uuid);
				$db->execute();
			}

			// if player has required energy, update database
			$db->query("UPDATE bl_users SET level = {$user['level']},
																			exp = {$user['exp']},
																			blood = {$user['blood']},
																			energyMaxedAt = {$user['energyMaxedAt']}
																  		WHERE uuid = (:uuid)");
			$db->bind(':uuid', $uuid);
			$db->execute();

			$adjustedmissionid = $id + 1;

			$db->query("SELECT m{$adjustedmissionid} FROM bl_users_missions WHERE uuid = :uuid");
			$db->bind(':uuid', $uuid);
			$mprog = $db->single();

			// update mission mastery if it's currently less than 100.
			if ($mprog['m'.$adjustedmissionid] < MAX_MASTERY) {
				$mprog['m'.$adjustedmissionid] += MASTERY_INCREASE_PER_MISSION;

				if ($mprog['m'.$adjustedmissionid] > 100) {
					$mprog['m'.$adjustedmissionid] = 100;
				}

				$db->query("UPDATE bl_users_missions SET m{$adjustedmissionid} = {$mprog['m'.$adjustedmissionid]}
																				 		 WHERE uuid = (:uuid)");
				$db->bind(':uuid', $uuid);
				$db->execute();

				// if mission is now mastered, give the player 3 SP
				if ($mprog['m'.$adjustedmissionid] == 100) {
					$db->query("UPDATE bl_users SET sp = sp + 3
																			WHERE uuid = (:uuid)");
					$db->bind(':uuid', $uuid);
					$db->execute();
				}

				$justmastered = true;
			}
			
					
			$ar = array('user' => $user, 'mission' => $mission, 'newmastery' => $mprog['m'.$adjustedmissionid], 'justmastered' => $justmastered);
		}

		break;
	}

	// foreach ($missions as $mission) {

	// 	if ($level >= $mission[2]) {

	// 		if (isset($mission[3])) {
	// 			foreach($mission[3] as $item => $amt) {
	// 				$v .= '<tr><td class="col-xs-1">'.$amt.' x '.itemName($item).'</td></tr>';
	// 			}
	// 		}						 

	// 	}
	// 	else {

	// 		$v .= '<div class="panel-body">
	// 					    <span class="grey-text">Unlocked at Level '.$mission[2].'</span>
	// 					 ';
	// 	}

	// }

  break;
}

echo json_encode($ar);