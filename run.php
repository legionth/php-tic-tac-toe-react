<?php

require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$socket = new React\Socket\Server('127.0.0.1:1080', $loop);
$gameServer = new \Legionth\TicTacToe\GameServer($socket);

echo "Game server is running on: " . $socket->getAddress() . PHP_EOL;
$loop->run();