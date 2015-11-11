<?php

namespace Worldstores\Battleships\Model;

/**
 * Class Board - representation of the game board
 *
 * @package Worldstores\Battleships\Model
 */
class Board
{
    // bit values to build the board matrix with
    const MATRIX_SHOT_BIT = 1;
    const MATRIX_SHIP_BIT = 2;

    /** @var Point - board width & height */
    private $dimension;

    /** @var Ship[] - ships on the board */
    private $ships;

    /** @var Shot[] - shots on the board */
    private $shots;

    /**
     * @param array $dimension
     */
    function __construct(array $dimension)
    {
        $this->dimension = new Point($dimension[0], $dimension[1]);
        $this->ships = [];
        $this->shots = [];
    }

    /**
     * Return an array representation of the board
     *
     * @return array
     */
    public function buildMatrix()
    {
        $matrix = [];

        for ($y = 1; $y <= $this->dimension->getY(); $y++) {
            $row = [];
            for ($x = 1; $x <= $this->dimension->getX(); $x++) {
                $point = new Point($x, $y);
                $cell = 0;

                // set SHOT_BIT on if there is a shot at $point
                if ($this->isShot($point)) {
                    $cell += self::MATRIX_SHOT_BIT;
                }

                // set SHIP_BIT on if there is a ship at $point
                if ($this->isShip($point)) {
                    $cell += self::MATRIX_SHIP_BIT;
                }

                $row[] = $cell;
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Return true when there is at least one shot at the given point
     *
     * @param Point $point
     * @return bool
     */
    public function isShot(Point $point)
    {
        foreach ($this->shots as $shot) {
            if ($shot->equalsTo($point)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if the given shot/point is already there
     *
     * @param Point $point
     * @return bool
     */
    public function hasShot(Point $point)
    {
        return $this->isShot($point);
    }

    /**
     * Return true when there is a ship at the given point
     *
     * @param Point $point
     * @return bool
     */
    public function isShip(Point $point)
    {
        foreach ($this->ships as $ship) {
            if ($ship->hasPoint($point)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a Ship if there is one having the given Point else null
     *
     * @param Point $point
     * @return null|Ship
     */
    public function getShipByPoint(Point $point)
    {
        foreach ($this->ships as $ship) {
            if ($ship->hasPoint($point)) {
                return $ship;
            }
        }

        return null;
    }

    /**
     * Return true if the points of the given ship are all hit
     *
     * @param Ship $ship
     * @return bool
     */
    public function isShipSunk(Ship $ship)
    {
        $hitsOnShip = 0;
        foreach ($this->shots as $shot) {
            if ($ship->hasPoint($shot)) {
                if (++$hitsOnShip >= $ship->getSize()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the number of hits
     *
     * @return int
     */
    public function getHits()
    {
        $hits = 0;
        foreach ($this->shots as $shot) {
            if ($this->isShip($shot)) {
                $hits++;
            }
        }

        return $hits;
    }

    /**
     * @return Point
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @param Point $dimension
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
    }

    /**
     * @return Ship[]
     */
    public function getShips()
    {
        return $this->ships;
    }

    /**
     * @param Ship[] $ships
     */
    public function setShips($ships)
    {
        $this->ships = $ships;
    }

    /**
     * Add a ship
     *
     * @param Ship $ship
     */
    public function addShip(Ship $ship)
    {
        $this->ships[] = $ship;
    }

    /**
     * @return Shot[]
     */
    public function getShots()
    {
        return $this->shots;
    }

    /**
     * @param Shot[] $shots
     */
    public function setShots($shots)
    {
        $this->shots = $shots;
    }

    /**
     * Add a shot
     *
     * @param Shot $shot
     */
    public function addShot(Shot $shot)
    {
        $this->shots[] = $shot;
    }


    /**
     * Return a very basic string view of the board
     * optimised for "Battleship Programming Test v1.pdf"
     *
     * @return string
     */
    public function __toString()
    {
        // write columns numbers
        $board = "\n" . '  ';
        for ($i = 1; $i <= $this->dimension->getX(); $i++) {
            $board .= substr($i, -1, 1);
        }
        $board .= PHP_EOL;

        // write grid
        for ($y = 1; $y <= $this->dimension->getY(); $y++) {
            // write row id
            $board .= chr(ord('A') + $y - 1) . ' ';

            for ($x = 1; $x <= $this->dimension->getX(); $x++) {
                $point = ' ';
                foreach ($this->ships as $ship) {
                    if ($ship->hasPoint(new Point($x, $y))) {
                        $point = 'X';
                        break;
                    }
                }
                $board .= $point;
            }
            $board .= PHP_EOL;
        }

        return $board;
    }
}
