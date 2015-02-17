<?php
include 'inc/functions.php';

$uuid = '123456789'; // test uuid

$action = $_GET['action'];

switch ($action) {

	case 'get-abilities':

	$abilities = array(array('Rise and Shine', 3, 1, 500), 
		  							 array('Silent Hunter', 6, 2, 1000),
		  							 array('Deadly Awaken', 10, 5, 2000));

	$ar = array('abilities' => $abilities);

	break;
}

echo json_encode($ar);