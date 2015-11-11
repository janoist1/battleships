<?php

namespace Worldstores\Battleships\Service;

use Worldstores\Battleships\Exception\GameException;
use Worldstores\Battleships\Model\Board;
use Worldstores\Battleships\Model\Line;
use Worldstores\Battleships\Model\Point;
use Worldstores\Battleships\Model\Ship;
use Worldstores\Battleships\Model\Shot;

/**
 * Class GameService - representation of the game logic
 *
 * @package Worldstores\Battleships\Service
 */
class GameService
{
    // this is a security limit of max. how many iterations can be done while randomly placing the ships to the board
    // it is used to secure the case when there is no space on the board to add more ships due to wrong settings config
    // it could be replaced by complex math algorithm that calculates if there was enough space to add more ships though ..
    const MAX_NUMBER_OF_ITERATIONS = 1000000;

    /** @var array - game settings */
    private $settings;

    /** @var Board - game board */
    private $board;

    /**
     * @param array $settings
     */
    function __construct(array $settings)
    {
        $this->settings = $settings;

        $this->validateSettings();
        $this->createBoard($settings['dimension']);
    }

    /**
     * Initialize the game
     * Pass ships and shots from session to continue a game
     *
     * @param array $gameData - load the game data
     */
    public function initialize(array $gameData = null)
    {
        if ($gameData === null) {
            // set up ships
            $this->addShips($this->settings['ships']);
        } else {
            // load back the game data
            $this->board->setShips($gameData['ships']);
            $this->board->setShots($gameData['shots']);
        }
    }

    /**
     * Restart the game by resetting its data
     */
    public function restart()
    {
        $this->board->setShips([]);
        $this->board->setShots([]);

        $this->initialize();
    }

    /**
     * Make a turn and return true if hit
     *
     * @param array $coordinate
     * @return bool
     * @throws GameException
     */
    public function turn(array $coordinate)
    {
        $shot = new Shot($coordinate[0], $coordinate[1]);
        $dimension = $this->board->getDimension();

        // check if shot is within the board
        if (1 > $shot->getX() || $shot->getX() > $dimension->getX()
            || 1 > $shot->getY() || $shot->getY() > $dimension->getY()
        ) {
            throw new GameException('Shot is out of the board');
        }

        // check if shot exists
        if ($this->board->hasShot($shot)) {
            throw new GameException('Already shot here');
        }

        $this->getBoard()->addShot($shot);

        return $this->getBoard()->isShip($shot);
    }

    /**
     * Return true when all the ships sunk
     *
     * @param array $coordinates
     * @return bool
     */
    public function isShipSunk(array $coordinates)
    {
        $ship = $this->board->getShipByPoint(new Point($coordinates[0], $coordinates[1]));

        return $this->board->isShipSunk($ship);
    }

    /**
     * Return true when all the ships sunk
     *
     * @return bool
     */
    public function isGameOver()
    {
        // number of hits required to win
        $hitsToWin = array_sum($this->settings['ships']);

        // lets not waste CPU time when there is not enough shot
        if (count($this->board->getShots()) < $hitsToWin) {
            return false;
        }

        // return true if we've got enough hits
        return $this->board->getHits() >= $hitsToWin;
    }

    /**
     * Return the number of shots
     *
     * @return int
     */
    public function getNumberOfShots()
    {
        return count($this->board->getShots());
    }

    /**
     * It allows to get the game data in order to store it and load it back later
     * (ex. store it in a session)
     *
     * @return array
     */
    public function getGameData()
    {
        return [
            'ships' => $this->getBoard()->getShips(),
            'shots' => $this->getBoard()->getShots(),
        ];
    }

    /**
     * Very basic validation for the settings
     *
     * @throws GameException
     */
    private function validateSettings()
    {
        if (!array_key_exists('dimension', $this->settings)
            || !is_array($this->settings['dimension'])
            || count($this->settings['dimension']) != 2
        ) {
            throw new GameException('Missing or wrong setting for "dimension"');
        }
        if (!array_key_exists('ships', $this->settings)
            || !is_array($this->settings['ships'])
            || count($this->settings['ships']) < 1
        ) {
            throw new GameException('Missing or wrong setting for "ships"');
        }
    }

    /**
     * Create the game board
     *
     * @param array $dimension
     */
    private function createBoard(array $dimension)
    {
        $this->board = new Board($dimension);
    }

    /**
     * Place ships randomly to the board
     *
     * @param array $ships
     */
    private function addShips(array $ships)
    {
        foreach ($ships as $size) {
            $this->addShip($size);
        }
    }

    /**
     * Add a ship with the given size randomly
     *
     * @param $size
     * @throws GameException
     */
    private function addShip($size)
    {
        $direction = rand(0, 1); // 0: horizontal, 1: vertical
        $iteration = 0;

        do {
            $overlap = false;
            $line = $this->createRandomLine($size, $direction);

            // we've got a line so lets check if there was any overlap with the existing ships
            foreach ($this->board->getShips() as $ship) {
                if ($ship->isOverlap($line)) {
                    $overlap = true;
                    break;
                }
            }

            // avoid killing the CPU ...
            if (++$iteration >= self::MAX_NUMBER_OF_ITERATIONS) {
                throw new GameException('MAX_NUMBER_OF_ITERATIONS reached');
            }
        } while ($overlap);

        $this->board->addShip(new Ship($line->getA(), $line->getB(), $size));
    }

    /**
     * Create random line based on the given size and direction
     *
     * @param int $size - length of the line
     * @param int $direction - 0: v, 1: h
     * @return Line
     */
    private function createRandomLine($size, $direction)
    {
        // based on the direction (0 or 1) we limit the maximum value of either a(x) or a(y) points
        // in order to not let their end points to be out of the coordinate system ..
        $sizeX = ($size - 1) * ((int)!$direction);
        $sizeY = ($size - 1) * $direction;

        $a = new Point(
            rand(1, $this->board->getDimension()->getX() - $sizeX),  // random x <= width - size * (0 | 1)
            rand(1, $this->board->getDimension()->getY() - $sizeY)   // random y <= height - size * (0 | 1)
        );
        $b = new Point(
            $a->getX() + $sizeX,
            $a->getY() + $sizeY
        );

        return new Line($a, $b);
    }

    /**
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }
}
