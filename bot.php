<?php
set_time_limit(0); // The script doesn't stop
ini_set('display_errors', 'on');

$config = array(
    'server' => 'irc.twitch.tv', //IRC Server
    'port' => 6667, // IRC server port
    'name' => '', //Bot's name
    'nick' => '', //Bot's name
    'password' => 'oauth:xxxx', //Bot oauth token,find it logging on http://twitchapps.com/tmi/
    'save' => ["config","socket"] //Here there are all important variables to save from the variable elimination after an update
);
include 'functions.php';
//login
$socket = fsockopen($config['server'], $config['port']);
sendData('PASS ' .$config['password']);
sendData('NICK ' . $config['nick']);
sendData('USER '. $config['nick'] . ' ' . $config['nick'] . ' ' . $config['nick'] . ' ' . $config['nick']);
sendData('CAP REQ :twitch.tv/commands'); //to receive private commands

//start bot loop
while (1) {
    $data = fgets($socket, 256);
    echo '\n IRC data: ' . $data;
    flush();
    //vars
    $comm = explode(":", $data);
    if (count($comm) > 1) {
    $username = str_beet($data, ":", "!");
        if (stripos($comm[1], "WHISPER") !== false) {
            $chat = str_beet($data, "WHISPER ", " ");
            $private = true;
        } elseif (stripos($comm[1], "PRIVMSG") !== false) {
            $chat = str_beet($data, "PRIVMSG #", " ");
        }
        $comm1 = $comm;
        unset($comm1[0]);
        unset($comm1[1]);
        $message = implode(':', $comm1);
        include "commands.php";
        echo "\n User: " . $username . " \n Chat: " . $chat . " \n Message " . $message;
    }
    //delete all variables created in the session
    $vars = array_keys(get_defined_vars());
    foreach ($vars as $var) {
        if (in_array($var, $config["save"])) {
        } else {
            unset($$var);
        }
    }
    unset($vars);
}
?>