<?php
//Some examples and generic commands
if (stripos($message, "!online") !== false) {
    if (isset($private)) {
        sendPrivateMessage($username,"Hello World!");
    } else {
        sendMessage($chat,"Hello World!");
    }
}
if (stripos($message, "!join") !== false) {
    $channel = str_replace("!join ","",$message);
    joinChannel('#' . $channel);
    if (isset($private)) {
        sendPrivateMessage($username,"I've just joined $channel!");
    } else {
        sendMessage($chat,"I've just joined $channel");
    }
}
if (stripos($message, "!say") !== false) {
    $text = str_replace("!say ","",$message);
    if (isset($private)) {
        sendPrivateMessage($username,$text);
    } else {
        sendMessage($chat,$text);
    }
}