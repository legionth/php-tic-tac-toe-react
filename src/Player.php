<?php

namespace Legionth\TicTacToe;

use React\Socket\ConnectionInterface;

class Player
{
    private $symbol;
    private $connection;

    public function __construct($symbol, ConnectionInterface $connection)
    {
        $this->symbol = substr($symbol, 0, 1);
        $this->connection = $connection;

        $this->connection->write('The new player selected the symbol: ' . $this->getSymbol());
        $this->connection->removeAllListeners();
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
