<?php


include($_SERVER['DOCUMENT_ROOT'].'/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /login/');
	exit;
} else {
    $user_id = $_SESSION['user_id'];

    echo $_POST['action']."<br />";
    echo "USER-ID: ".$user_id;

    $query_get_action_id = $db->prepare("SELECT `id` FROM `actions` WHERE `name`=:action_name");
    $query_get_action_id->execute([
        ':action_name' => $_POST['action'],
    ]);
    $action_id = $query_get_action_id->fetch()['id'];

    $query_set_action = $db->prepare("UPDATE `players` SET `last_action`=:last_action WHERE `user`=:user");
    $query_set_action->execute([
        ':last_action' => $action_id,
        ':user' => $user_id,
    ]);
}
