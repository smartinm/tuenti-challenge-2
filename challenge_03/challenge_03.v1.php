<?php

/**
 * Challenge 3: The evil trader
 */

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$ms = 0;
$input = array();
foreach ($lines as $line) {
  $input[$ms] = $line;
  $ms += 100;
}

if (count($input)) {
  $output = maximum_gain($input);
  echo $output['buy'] . ' ' . $output['sell'] . ' ' . $output['gain'];
}

function maximum_gain($input) {
  $sorted = $input;
  arsort($sorted, SORT_NUMERIC);

  $buy = 0;
  $sell = 0;
  $gain = 0;

  foreach ($input as $time => $value) {
    $max_value = reset($sorted);
    if ($max_value - $value > $gain) {
      $buy = $time;
      $sell = key($sorted);
      $gain = $max_value - $value;
    }
    unset($sorted[$time]);
  }

  return array(
    'buy' => $buy,
    'sell' => $sell,
    'gain' => $gain,
  );
}
