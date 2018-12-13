<?php
$lines = file(__DIR__ . '/firstInput.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
$map = [];
$isCollision = false;
$cartDecoder = ['<' => [-1,0],'>' => [1,0],'^' => [0,-1],'v' => [0,1]];
$mapDecoder = ['/' => 1, '\\' => 2, '+' => 3, '-' => 0, '|' => 0];
$carts = [];
$y = 0;
foreach ($lines as $line) {
    for ($x = 0; $x < strlen($line); $x++) {
        if (array_key_exists($line{$x}, $cartDecoder)) {
            $map[$y][$x] = 0;
            $speed = $cartDecoder[$line{$x}];
            array_push($carts, [$x, $y, $speed[0], $speed[1], 0]);
        } else if (array_key_exists($line{$x}, $mapDecoder)) {
            $map[$y][$x] = $mapDecoder[$line{$x}];
        }
    }
    $y++;
}
while (!$isCollision) {
    usort($carts, function($a, $b) {
        return $a[1] === $b[1] ? ($a[0] - $b[0]) : ($a[1] - $b[1]);
    });
    foreach ($carts as $idx => $cart) {
        $cart[0] += $cart[2];
        $cart[1] += $cart[3];
        if ($map[$cart[1]][$cart[0]] === 1) {
            $tmp = $cart[2];
            $cart[2] = -$cart[3];
            $cart[3] = -$tmp;
        } else if ($map[$cart[1]][$cart[0]] === 2) {
            $tmp = $cart[2];
            $cart[2] = $cart[3];
            $cart[3] = $tmp;
        } else if ($map[$cart[1]][$cart[0]] === 3) {
            if ($cart[4] !== 1) {
                $cart[2] = $cart[2] === 0
                    ? ($cart[3] === 1 ? ($cart[4] === 0 ? 1 : -1) : ($cart[4] === 0 ? -1 : 1))
                    : 0;
                $cart[3] = $cart[3] === 0
                    ? ($cart[2] === 1 ? ($cart[4] === 0 ? 1 : -1) : ($cart[4] === 0 ? -1 : 1))
                    : 0;
            }
            $cart[4] = ($cart[4] + 1) % 3;
        }
        $carts[$idx] = $cart;
        for ($j = 0; $j < count($carts); $j++) {
            if ($j !== $idx && $carts[$j][0] === $carts[$idx][0] && $carts[$j][1] === $carts[$idx][1]) {
                echo $carts[$j][0] . ',' . $carts[$j][1] . PHP_EOL;
                $isCollision = true;
                break 2;
            }
        }
    }
}
