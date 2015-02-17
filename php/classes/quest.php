<?php

Class Quest {

	private $_db;

	public function setDb($db) {
		$this->_db = $db->conn;
	}

	public function getQuests() {

		$quests = array(
			array('id' => 1, 'name' => 'Quest 1', 'exp' => 1, 'blood' => 5, 'energyRequired' => 1),
			array('id' => 2, 'name' => 'Quest 2', 'exp' => 2, 'blood' => 10, 'energyRequired' => 2),
			array('id' => 3, 'name' => 'Quest 3', 'exp' => 3, 'blood' => 15, 'energyRequired' => 3),
			array('id' => 4, 'name' => 'Quest 4', 'exp' => 4, 'blood' => 20, 'energyRequired' => 4),
			array('id' => 5, 'name' => 'Quest 5', 'exp' => 5, 'blood' => 25, 'energyRequired' => 5)
		);

		return $quests;
	}

	public function doQuest($id) {

		$ar = array();

		$quest['id'] = 1;
		$quest['exp'] = 1;
		$quest['blood'] = 5;
		$quest['energyUsed'] = 1;

		$result = $this->_db->query("SELECT exp FROM users WHERE uuid = 123456789");
		$user = $result->fetch_array(MYSQLI_ASSOC);

		$user['exp'] += $quest['exp'];

		$this->_db->query("UPDATE users SET exp = {$user['exp']} WHERE id = 1");

		if ($user['exp'] >= (5 * $user['level'])) {

			$user['level'] += 1;
			$user['exp'] = $user['exp'] - $user['exp_level'];
			$user['exp_level'] = $user['exp_level'] + $user['level'];

			$this->_db->query("UPDATE users SET level = {$user['level']}, exp = {$user['exp']} WHERE id = 1");
		}

		$ar['user'] = $user;
		$ar['quest'] = $quest;

		return $ar;
	}
}