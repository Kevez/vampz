<?php

include_once '../../classes/db.php';
include_once '../../classes/quest.php';

$db = new Db();

$quest = new Quest();
$quest->setDb($db);
$quest = $quest->getQuests();

echo json_encode($quest);