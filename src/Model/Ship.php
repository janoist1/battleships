<?php

namespace Worldstores\Battleships\Model;

/**
 * Class Ship - representation of a ship
 *
 * @package Worldstores\Battleships\Model
 */
class Ship extends Line
{
    /** @var int - the size of the ship */
    private $size;

    /**
     * @param Point $a
     * @param Point $b
     * @param int $size
     */
    function __construct(Point $a, Point $b, $size)
    {
        parent::__construct($a, $b);

        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}
