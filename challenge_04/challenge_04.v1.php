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
  $info   = explode(' ', $input['info']);
  $groups = explode(' ', $input['groups']);

  $result = test_case($info, $groups);
  echo $result . "\n";
}

function test_case($info, $groups) {
  list($r, $k, $g) = $info;

  if ($r <= 0 || $k <= 0 || count($groups) != $g) {
    return 0;
  }

  $gasoline = 0;
  $queue = $groups;

  // Start races
  for ($i = 0; $i < $r; $i++) {
    // Prepare race
    $people = 0;
    $current = $queue;

    foreach ($current as $size) {
      if ($people + $size > $k) {
        break;
      }
      $people += $size;
      $group = array_shift($queue);
      array_push($queue, $group);
    }

    // Run!
    $gasoline += $people;
  }

  return $gasoline;
}
