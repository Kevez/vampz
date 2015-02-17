<?php

Class User {

	private $_db;

	public function setDb($db) {
		$this->_db = $db->conn;
	}

	public function getStats($uuid) {

		$user = array();

		$result = $this->_db->query("SELECT level, exp, blood, max_energy FROM users WHERE uuid = {$uuid}");
		$user = $result->fetch_array(MYSQLI_ASSOC);

		// calc'd on fly
		$user['blood'] = number_format($user['blood']);
		$user['exp_level'] = $user['level'] * 5;
		$user['energy'] = 10;

		return $user;
	}

	public function updateStat($field, $newVal) {

		// $result = $this->_db->query("SELECT id, username FROM users WHERE id = 1");
		// while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		// 	array_push($user, $row);
		// }

		$this->_db->query("UPDATE users SET $field = $newVal WHERE id = 1");

		//return $user;
	}

	// public function getPost($id) {
	// 	$result = $this->_db->query("SELECT id, title, content, created FROM posts WHERE id = {$id}");
	// 	$post = $result->fetch_array(MYSQLI_ASSOC);

	// 	$post['created'] = date('D d M Y H:i', $post['created']);
		
	// 	return $post;
	// }

	// public function addPost($title, $content) {

	// 	$posted = false;
	// 	$created = time();

	// 	$result = $this->_db->query("INSERT INTO posts (title, content, created) VALUES ('$title', '$content', $created)");

	// 	if ($result) {
	// 		$posted = true;
	// 	}
		
	// 	return $posted;
	// }

	// public function editPost($id, $title, $content) {

	// 	$updated = false;

	// 	$result = $this->_db->query("UPDATE posts SET title = '$title', content = '$content' WHERE id = $id");

	// 	if ($result) {
	// 		$updated = true;
	// 	}
		
	// 	return $updated;
	// }
}