<?php


ob_implicit_flush();

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (!$socket) {
    echo "Hiba!";
} else {
    $result = socket_connect($socket, '192.135.133.80', 6571);

    echo "Ok";
}
