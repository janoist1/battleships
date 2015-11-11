<?php

namespace Worldstores\Battleships\Model;

/**
 * Class Point - representation of a point
 *
 * @package Worldstores\Battleships\Model
 */
class Point
{
    /** @var int - x coordinate */
    private $x;

    /** @var int - y coordinate */
    private $y;

    /**
     * @param int $x
     * @param int $y
     */
    function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Return true if the given point is in the same location
     *
     * @param Point $point
     * @return bool
     */
    public function equalsTo(Point $point)
    {
        return $this->x == $point->getX() && $this->getY() == $point->getY();
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }
}
