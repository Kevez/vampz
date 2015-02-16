<?php

include_once '../../classes/db.php';
include_once '../../classes/quest.php';
include_once '../../classes/user.php';

$db = new Db();

$quest = new Quest();
$quest->setDb($db);
$quest = $quest->doQuest(1);

echo json_encode($quest);