<?php
//functions
function str_beet($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function sendData($command) //send an IRC command
{
    global $socket;
    fputs($socket, $command . "\r\n");
    echo "\n>Sent data: " . $command;

}
function joinChannel($channel) //join into a channel
{
    global $socket;
    sendData('JOIN '. strtolower($channel));
}
function sendPrivateMessage($username,$message) //send private message to a user
{
    global $socket;
    sendData('PRIVMSG #' . $username . ' :/w ' . $username . ' ' . $message);
}
function sendMessage($chat,$message) //send a message in a channel
{
    global $socket;
    sendData('PRIVMSG #' . $chat . ' : ' . $message);
}

