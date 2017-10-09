<?php

namespace Legionth\TicTacToe;

class GameField
{
    private $field;

    /** @var Player[]  */
    private $players = array();

    public function __construct($xFields = 3, $yFields = 3)
    {
        $this->field = array(array());
        for ($y = 0; $y < $yFields; $y++) {
            for ($x = 0; $x < $xFields; $x++) {
                $this->field[$y][$x] = ' ';
            }
        }
    }

    public function setSymbol($symbol, $x, $y)
    {
        $this->field[$y][$x] = $symbol;
    }

    public function getField()
    {
        $field = PHP_EOL;
        for ($y = 0; $y < count($this->field); $y++) {
            $field .= $this->getDelimiter();

            for ($x = 0; $x < count($this->field[$y]); $x++) {
                $field .= '|' . $this->field[$y][$x] . '|';
            }
            $field .=  PHP_EOL;
        }
        $field .= $this->getDelimiter();

        return $field;
    }

    /**
     * @param Player $player - new player to join the game, after two player joined
     *                         the game will start
     */
    public function addPlayer(Player $player)
    {
        $this->players[] = $player;
        if (count($this->players) === 2) {
            $this->startGame();
        }
    }

    public function startGame()
    {
        $field = $this->getField();
        foreach ($this->players as $player) {
            $connection = $player->getConnection();
            $connection->write($field);
        }

        $this->nextPlayerTurn($this->players[0], $this->players[1]);
    }

    /** @internal */
    public function nextPlayerTurn(Player $currentPlayer, Player $nextPlayer)
    {
        $that = $this;

        $currentPlayer->getConnection()->write('Please enter the coordinates in the following format: x y');
        $currentPlayer->getConnection()->write('Your input:');


        $currentPlayer->getConnection()->on('data', function ($data) use ($that, $currentPlayer, $nextPlayer) {
            $data = trim($data);
            $x = substr($data, 0, 1);
            $y = substr($data, 2, 1);

            if (!$that->isCoordinateValid($x, $y)) {
                return $currentPlayer->getConnection()->write('Invalid coordinates please try again');
            }

            $that->setSymbol($currentPlayer->getSymbol(), $x, $y);

            $currentPlayer->getConnection()->write($that->getField());
            $nextPlayer->getConnection()->write($that->getField());
            $result = $that->isWinner($currentPlayer->getSymbol());

            if ($result === false) {
                $currentPlayer->getConnection()->removeAllListeners('data');
                return $that->nextPlayerTurn($nextPlayer, $currentPlayer);
            }
            $currentPlayer->getConnection()->write('YOU WON!');
            $nextPlayer->getConnection()->write('YOU LOST!');
        });
    }

    /** @internal  */
    public function isWinner($symbol)
    {
        $winningString = str_repeat($symbol, 3);

        for ($y = 0; $y < count($this->field); $y++) {
            $row = '';
            for ($x = 0; $x < count($this->field[$y]); $x++) {
                $row .= $this->field[$y][$x];
                if ($row === $winningString) {
                    return true;
                }
            }
        }

        for ($x = 0; $x < count($this->field[0]); $x++) {
            $column = '';
            for ($y = 0; $y < count($this->field); $y++) {
                $column .= $this->field[$y][$x];
                if ($column === $winningString) {
                    return true;
                }
            }
        }

        $diagonal1 = $this->field[0][0] . $this->field[1][1] . $this->field[2][2];
        $diagonal2 = $this->field[0][2] . $this->field[1][1] . $this->field[2][0];

        if ($diagonal1 === $winningString || $diagonal2 === $winningString) {
            return true;
        }

        return false;
    }

    /** @internal */
    public function isCoordinateValid($x, $y)
    {
        if ($x < 0 || $x >= 3) {
            return false;
        }

        if ($y < 0 || $y >= 3) {
            return false;
        }

        if ($this->field[$y][$x] === ' ') {
            return false;
        }

        return true;
    }

    private function getDelimiter()
    {
        $delimiter = '';
        for ($x = 0; $x < count($this->field[0]); $x++) {
            $delimiter .= '---';
        }
        $delimiter .= PHP_EOL;

        return $delimiter;
    }
}
