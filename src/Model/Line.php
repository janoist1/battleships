<?php

namespace Worldstores\Battleships\Model;

/**
 * Class Line - representation of a line
 *
 * @package Worldstores\Battleships\Model
 */
class Line
{
    /** @var Point - coordinates of a */
    private $a;

    /** @var Point - coordinates of b */
    private $b;

    /**
     * @param Point $a
     * @param Point $b
     */
    function __construct(Point $a, Point $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * Return true if the given point is on the line
     *
     * @param Point $point
     * @return bool
     */
    public function hasPoint(Point $point)
    {
        $nv = $this->getNormalVector();

        if ($nv->getX() * $point->getX() + $nv->getY() * $point->getY()
            != $nv->getX() * $this->a->getX() + $nv->getY() * $this->a->getY()
        ) {
            return false;
        }

        return $point->getX() >= $this->a->getX() && $point->getX() <= $this->b->getX()
        && $point->getY() >= $this->a->getY() && $point->getY() <= $this->b->getY();
    }

    /**
     * Return true if the given line intersect/overlap
     *
     * @param Line $line
     * @return bool
     */
    public function isOverlap(Line $line)
    {
        $nvA = $this->getNormalVector();
        $nvB = $line->getNormalVector();

        // line equation 1
        $a1 = $nvA->getX();
        $b1 = $nvA->getY();
        $c1 = $nvA->getX() * $this->a->getX() + $nvA->getY() * $this->a->getY();

        // line equation 2
        $a2 = $nvB->getX();
        $b2 = $nvB->getY();
        $c2 = $nvB->getX() * $line->a->getX() + $nvB->getY() * $line->a->getY();

        // Calculation cheat: http://www.thelearningpoint.net/computer-science/c-program-solving-simultaneous-equations-in-two-variables
        if ((($a1 * $b2 - $a2 * $b1) != 0) && (($b1 * $a2 - $b2 * $a1) != 0)) {
            // we have a unique solution
            $x = ($c1 * $b2 - $c2 * $b1) / ($a1 * $b2 - $a2 * $b1);
            $y = ($c1 * $a2 - $c2 * $a1) / ($b1 * $a2 - $b2 * $a1);

            // they intersect so let's check if that intersection point is on the line section
            return $this->hasPoint(new Point($x, $y));

        } elseif ((($a1 * $b2 - $a2 * $b1) == 0) && (($b1 * $a2 - $b2 * $a1) == 0)
            && (($c1 * $b2 - $c2 * $b1) == 0) && (($c1 * $a2 - $c2 * $a1) == 0)
        ) {
            // infinite solutions
            return true;
        }

        // no solution: the lines don't intersect each other

        return false;
    }

    /**
     * p(x2-x1; y2-y1)
     * AB = b-a
     *
     * @return Point
     */
    public function getPositionVector()
    {
        return new Point($this->b->getX() - $this->a->getX(), $this->b->getY() - $this->a->getY());
    }

    /**
     * n(y2-y1; -(x2-x1))
     * rotate position vector by 90 degrees
     *
     * @return Point
     */
    public function getNormalVector()
    {
        $p = $this->getPositionVector();

        return new Point($p->getY(), $p->getX() * (-1));
    }

    /**
     * @return Point
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @param Point $a
     */
    public function setA($a)
    {
        $this->a = $a;
    }

    /**
     * @return Point
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @param Point $b
     */
    public function setB($b)
    {
        $this->b = $b;
    }
}
