<?php
header("Access-Control-Allow-Origin: *");
// include database class
include 'db.class.php';


$db = new Db();

$tstamp = time();
define(ENERGY_WAIT_PER_INCREASE, 15);

function itemName ($id) {

	switch ($id) {
		case 1: $name = 'Secret Whisper'; break;
		case 2: $name = 'Heavy Hand'; break;
		case 3: $name = 'Dash'; break;
	}

	return $name;
}

function calculateRemainingEnergy ($secondsToMaxEnergy, $maxEnergy) {

	$energy = $maxEnergy;

	while ($secondsToMaxEnergy > 0) {
		$energy--;
		$secondsToMaxEnergy -= ENERGY_WAIT_PER_INCREASE;
	}

	return $energy;
}

function missionData($id) {
	
	// title // blood // level req
	$missions = array();
	$missions[0] = array('Drain an Animal', 30, 1, array(1 => 2, 2 => 4));
	$missions[1] = array('Stalk a Human', 60, 2, array(1 => 5));
	$missions[2] = array('Subdue a Criminal', 90, 3);
	$missions[3] = array('Evade Hunters', 120, 4);
	$missions[4] = array('Ward Off Wolf Pack', 150, 30);
	$missions[5] = array('Claim Territory', 180, 6);
	$missions[6] = array('Feed on Humans', 210, 7);
	$missions[7] = array('Terrorize the Park', 240, 8);
	$missions[8] = array('Test 9', 270, 9);
	$missions[9] = array('Test 10', 300, 10);
	$missions[10] = array('Test 11', 300, 11);
	$missions[11] = array('Test 12', 300, 1);
	$missions[12] = array('Test 13', 300, 13);
	$missions[13] = array('Test 14', 300, 14);
	$missions[14] = array('Test 15', 300, 15);
	$missions[15] = array('Test 16', 300, 16);
	$missions[16] = array('Test 17', 300, 17);
	$missions[17] = array('Test 18', 300, 18);
	$missions[18] = array('Test 19', 300, 19);
	$missions[19] = array('Test 20', 300, 20);
	$missions[20] = array('Test 21', 300, 10);
	$missions[21] = array('Test 22', 300, 10);
	$missions[22] = array('Test 23', 300, 10);
	$missions[23] = array('Test 24', 300, 10);
	$missions[24] = array('Test 25', 300, 10);
	$missions[25] = array('Test 26', 300, 10);
	$missions[26] = array('Test 27', 300, 10);
	$missions[27] = array('Test 28', 300, 10);
	$missions[28] = array('Test 29', 300, 10);
	$missions[29] = array('Test 30', 300, 10);
	$missions[30] = array('Test 31', 300, 10);
	$missions[31] = array('Test 32', 300, 10);
	$missions[32] = array('Test 33', 300, 10);
	$missions[33] = array('Test 34', 300, 10);
	$missions[34] = array('Test 35', 300, 10);
	$missions[35] = array('Test 36', 300, 10);
	$missions[36] = array('Test 37', 300, 10);
	$missions[37] = array('Test 38', 300, 10);
	$missions[38] = array('Test 39', 300, 10);
	$missions[39] = array('Test 40', 1000, 10);
	
	return $missions;
}

function expLevelLimit($level) {
	return 3 + ceil($level * 1.4);

	/*
	Lvl 1 = 5
	Lvl 2 = 6
	Lvl 3 = 8
	Lvl 4 = 9
	Lvl 5 = 12
	*/
}

function skillUpgradeCost($id) {
	return 1;
}