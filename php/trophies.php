<?php
include '../inc/functions.php';

$uuid = '123456789'; // test uuid

$action = $_GET['action'];

switch ($action) {

  case null:
  {

    // 
    $user = 1;    

    $ar = array('user' => $user);

    break;
  }
}

echo json_encode($ar);