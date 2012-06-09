<?php

/**
 * Challenge 10: Coding m00re and m00re
 */

/**************************************
  x y &             x / y
  x y #             x*y
  x mirror          -x
  x y conquer       x % y
  x y $             x - y
  x y dance $       y - x
  x y @             x + y
  x y @ 1 $         (x + y) - 1

  x breadandfish    x x
  x fire

**************************************/

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
  $tokens = parser($line);
  $tokens = preprocess($tokens);
  $result = m00re($tokens);
  echo $result . "\n";
}

function parser($input) {
  $input = trim($input, ' .');
  return explode(' ', $input);
}

function preprocess($tokens) {
  $processed = array();
  for ($i = 0; $i < count($tokens); $i++) {
    $token = $tokens[$i];
    if ($token === 'breadandfish') {
      if ($i > 0) {
        $processed[] = $tokens[$i - 1];
      }
    }
    elseif ($token === 'fire') {
      array_pop($processed);
    }
    else {
      $processed[] = $token;
    }
  }
  return array_reverse($processed);
}

function m00re(&$tokens, &$dance = FALSE) {
  if (count($tokens) > 0) {
    $c = array_shift($tokens);
    switch ($c) {
      case '@':
        $y = m00re($tokens, $dance);
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcadd($x, $y);
      case '$':
        $y = m00re($tokens, $dance);
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcsub($x, $y);
      case '&':
        $y = m00re($tokens, $dance);
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcdiv($x, $y);
      case '#':
        $y = m00re($tokens, $dance);
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcmul($x, $y);
      case 'conquer':
        $y = m00re($tokens, $dance);
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcmod($x, $y);
      case 'mirror':
        $x = m00re($tokens, $dance);
        if ($dance) { list($x, $y) = array($y, $x); $dance = FALSE; };
        return bcsub(0, $x);
      case 'dance':
        $x = m00re($tokens, $dance);
        $dance = TRUE;
        return $x;
      default:
        return $c;
    }
  }
}
