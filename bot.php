<?php
set_time_limit(0); // The script doesn't stop
$config = array(
    'server' => 'irc.twitch.tv', //IRC Server
    'port' => 6667, // IRC server port
    'name' => 'username', //Bot's name
    'nick' => 'username', //Bot's name
    'password' => 'oauth:xxxx', //Bot oauth token,find it logging on http://twitchapps.com/tmi/
    'save' => ["config", "socket"] //Here there are all important variables to save from the variable elimination after an update
);
include 'functions.php';
//login
$socket = fsockopen($config['server'], $config['port']);
sendData('PASS ' . $config['password']);
sendData('NICK ' . $config['nick']);
sendData('USER ' . $config['nick'] . ' ' . $config['nick'] . ' ' . $config['nick'] . ' ' . $config['nick']);
sendData('CAP REQ :twitch.tv/commands'); //to receive private commands
sendData('CAP REQ :twitch.tv/tags'); //to get user color, badge
sendData('CAP REQ :twitch.tv/membership'); //membership

//start bot loop
while (1) {
    $data = fgets($socket, 512);
    file_put_contents("log", file_get_contents("log") . "\n$data");
    echo "\n\n IRC data: " . $data . PHP_EOL;
    flush();
    sendData("PONG");
    //vars
    
    if (stripos($data, ":") === 0) {
        $username = str_beet($data, ":", "!");
        if (stripos($data, "JOIN") !== false) {
            $chat = str_beet($data . ' ', "JOIN #", " ");
            file_put_contents("log", file_get_contents("log") . "\n$username JOINED $chat");
            $isjoined = true;
        } elseif (stripos($data, "PART") !== false) {
            $chat = str_beet($data . ' ', "PART #", " ");
            file_put_contents("log", file_get_contents("log") . "\n$username Disconnected $chat");
            $isdisconnected = true;
        }
    } elseif (stripos($data, "@") === 0) {
        $ex = explode(";", $data, 13);
        $update = [];
        foreach ($ex as $info) {
            $eq = explode("=", $info, 2);
            $key = $eq[0];
            $value = $eq[1];
            $update[$key] = $value;
        }
        $comm = explode(":", $update['user-type']);
        $username = str_beet($update['user-type'], ":", "!");
        if (stripos($comm[1], "WHISPER") !== false) {
            $chat = str_beet($update['user-type'], "WHISPER ", " ");
            $private = true;
        } elseif (stripos($comm[1], "PRIVMSG") !== false) {
            $chat = str_beet($update['user-type'], "PRIVMSG #", " ");
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
