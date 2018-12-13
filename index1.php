<?php
$carts = $map = [];
$cartDecoder = ['<' => 180,'>' => 0, '^' => 270, 'v' => 90];
$mapDecoder = ['/' => 1, '\\' => 2, '+' => 3, '-' => 0, '|' => 0];
$y = 0;
$flags = FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES;
$lines = file(__DIR__ . '/input.txt', $flags);
foreach ($lines as $line) {
    for ($x = 0; $x < strlen($line); $x++) {
        if (array_key_exists($line{$x}, $cartDecoder)) {
			$map[$y][$x] = 0;
			$direction = $cartDecoder[$line{$x}];
			array_push($carts, [$x, $y, $direction, 0]);
        } else if (array_key_exists($line{$x}, $mapDecoder)) {
			$map[$y][$x] = $mapDecoder[$line{$x}];
        }
    }
    $y++;
}
$turns = [0 => [1 => 270, 2 => 90], 90 => [1 => 180, 2 => 0], 180 => [1 => 90, 2 => 270], 270 => [1 => 0, 2 => 180]];
$isCollision = false;
while ($isCollision === false) {
    usort($carts, function($a, $b) {
        return $a[1] === $b[1] ? ($a[0] - $b[0]) : ($a[1] - $b[1]);
    });
    foreach ($carts as $idx => $cart) {
        $cart[0] += round(cos(deg2rad($cart[2])));
        $cart[1] += round(sin(deg2rad($cart[2])));
        if ($map[$cart[1]][$cart[0]] === 1 || $map[$cart[1]][$cart[0]] === 2) {
            $cart[2] = $turns[$cart[2]][$map[$cart[1]][$cart[0]]];
		} else if ($map[$cart[1]][$cart[0]] === 3) {
			if ($cart[3] === 0) {
				$cart[2] -= 90;
			} else if ($cart[3] === 2) {
				$cart[2] += 90;
			}
            $cart[3] = ($cart[3] + 1) % 3;
        }
		// The degree fix
		if ($cart[2] === 360) $cart[2] = 0;
		if ($cart[2] < 0) $cart[2] += 360;
        $carts[$idx] = $cart;
		echo implode(', ', $cart) . PHP_EOL;
        for ($i = 0; $i < count($carts); $i++) {
            if ($i !== $idx && $carts[$i][0] === $carts[$idx][0] && $carts[$i][1] === $carts[$idx][1]) {
                echo 'The first collision: ' . $carts[$i][0] . ',' . $carts[$i][1] . PHP_EOL;
                $isCollision = true;
                break 2;
            }
        }
    }
}
