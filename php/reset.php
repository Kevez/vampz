<?php
include 'inc/functions.php';

$db->query('DELETE FROM bl_users');
$db->execute();

$db->query('DELETE FROM bl_users_abilities');
$db->execute();

$db->query('DELETE FROM bl_users_coven');
$db->execute();

$db->query('DELETE FROM bl_users_missions');
$db->execute();