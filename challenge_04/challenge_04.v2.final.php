<?php

/**
 * Challenge 4: 20 fast 20 furious
 */

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$test_cases = array();

$count = NULL;
for($i = 0; $i < count($lines); $i++) {
  $line = $lines[$i];
  if (!isset($count)) {
    $count = intval($line);
  }
  else if (count($test_cases) < $count) {
    $test_cases[] = array(
      'info' => $line,
      'groups' => isset($lines[$i+1]) ? $lines[$i+1] : array(),
    );
    $i++;
  }
}

foreach ($test_cases as $input) {
  $info   = explode(' ', trim($input['info']));
  $groups = explode(' ', trim($input['groups']));

  $result = test_case($info, $groups);
  echo $result . "\n";
}

function test_case($info, $groups) {
  list($r, $k, $g) = $info;
  if ($r <= 0 || $k <= 0 || count($groups) != $g) {
    return 0;
  }

  $cache = array();
  for ($i = 0; $i < count($groups); $i++) {
    $people = 0;
    $karts = 0;
    for ($j = $i; ($j < $i + $g) && ($people < $k); $j++) {
      $size = $groups[$j % $g];
      if ($people + $size > $k) {
        break;
      }
      $people += $size;
      $karts ++;
    }
    $cache[$i] = array($people, $karts);
  }

  $gasoline = 0;
  $index = 0;
  $period = array();
  for ($num_races = 0; $num_races < $r; $num_races++) {
    $info = $cache[$index];
    if (!isset($period[$index])) {
      $period[$index] = $info[0];
      $gasoline = bcadd($gasoline, $info[0]);
      $index = ($index + $info[1]) % $g;
    }
    else {
      // TODO Need comments!!!
      $keys = array_keys($period);
      $range = array_slice($period, array_search($index, $keys));
      $range_gasoline = 0;
      foreach ($range as $x) {
        $range_gasoline = bcadd($range_gasoline, $x);
      }
      $races = $r - $num_races;
      $r1 = floor($races / count($range));
      $r2 = $races % count($range);
      $gasoline = bcadd($gasoline, bcmul($r1, $range_gasoline));
      for ($i = 0; $i < $r2; $i++) {
        $gasoline = bcadd($gasoline, $range[$i]);
      }
      break;
    }
  }

  return $gasoline;
}
