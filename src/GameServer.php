<?php

namespace Legionth\TicTacToe;

use React\Socket\ConnectionInterface;
use React\Socket\Server;

class GameServer
{
    private $field;
    private $socket;

    public function __construct(Server $socket)
    {
        $this->field = new GameField();
        $this->socket = $socket;

        $socket->on('connection', array($this, 'handleConnection'));
    }

    public function handleConnection(ConnectionInterface $connection)
    {
        $field = $this->field;

        $connection->write('Welcome the game!' . PHP_EOL .' The Game is about to start PLEASE enter a character.' . PHP_EOL . 'This will be your symbol for the game.');

        $connection->on('data', function ($data) use ($connection, &$field){
            $player = new Player($data, $connection);
            $field->addPlayer($player);
        });
    }
}
