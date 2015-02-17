<?php
include 'inc/functions.php';

$db->query('DELETE FROM users');
$db->execute();

$db->query('DELETE FROM users_abilities');
$db->execute();

$db->query('DELETE FROM users_coven');
$db->execute();

$db->query('DELETE FROM users_missions');
$db->execute();