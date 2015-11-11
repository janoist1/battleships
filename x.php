<?php


$a1 = 3;
$b1 = 2;
$c1 = 14;

$a2 = 1;
$b2 = 4;
$c2 = 8;

$det = $a1 * $b2 - $a2 * $b1;
$det1 = $c1 * $b2 - $c2 * $b1;
$det2 = $a1 * $c2 - $a2 * $c1;
$x = ($c1 * $b2 - $c2 * $b1) / ($a1 * $b2 - $a2 * $b1);
$y = ($c1 * $a2 - $c2 * $a1) / ($b1 * $a2 - $b2 * $a1);

if ($det == 0) {
    echo 'nincs mo.';
} else {
    echo 'x = ' . $x . ', y = ' . $y;
}

echo PHP_EOL;

die();

class Line
{
    private $a;
    private $b;

    function __construct(array $a, array $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    function getPositionVector()
    {
        return [$this->b[0] - $this->a[0], $this->b[1] - $this->a[1]];
    }

    function getNormalVector()
    {
        $p = $this->getPositionVector();

        return [$p[1], $p[0] * (-1)];
    }

    function isPointOn(array $p)
    {
        $nv = $this->getNormalVector();

        return $nv[0] * $p[0] + $nv[1] * $p[1] == $nv[0] * $this->a[0] + $nv[1] * $this->a[1];
    }
}

$p = [2, 1];
$q = [8, 4];
$r = [16, 8];
$line = new Line($p, $q);

$pv = $line->getPositionVector();
$nv = $line->getNormalVector();

echo 'pv iranyvektor: ' . $pv[0] . ';' . $pv[1] . "\n";
echo 'nv normalvektor: ' . $nv[0] . ';' . $nv[1] . "\n";
//echo 'Ax + By + C = 0.' . "\n";
//echo 'A*x + B*y = A*P(x) + B' . "\n";
echo 'egyenlet: ' . $nv[0] . 'x + ' . $nv[1] . 'y = ' . $nv[0] . '*' . $p[0] . ' + ' . $nv[1] . '*' . $p[1] . "\n";
echo '          ' . $nv[0] . 'x + ' . $nv[1] . 'y = ' . $nv[0] * $p[0] . ' + ' . $nv[1] * $p[1] . "\n";
echo '          ' . $nv[0] . 'x + ' . $nv[1] . 'y = ' . ($nv[0] * $p[0] + $nv[1] * $p[1]) . "\n";

echo 'isPointOn ' . $r[0] . ';' . $r[1] . ': ' . ($line->isPointOn($r) ? 'y' : 'n') . "\n";

die();

$max = 100000000;
$r = 0;
$nv = rand(0, $max);
$start = microtime(true);
$guesses = 0;

echo "guessing has begun ...";

do {
    $r = rand(0, $max);
    $guesses++;
} while ($nv != $r);

echo " done!\n";
echo " - it took " . (microtime(true) - $start) . " seconds and {$guesses} guesses\n\n";
