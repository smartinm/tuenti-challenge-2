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
  uasort($sorted, 'rbccomp');

  $buy  = 0;
  $sell = 0;
  $gain = 0;

  foreach ($input as $time => $value) {
    $max_value = reset($sorted);
    $aux_gain = bcsub($max_value, $value);
    if (bccomp($aux_gain, $gain) === 1) {
      $buy = $time;
      $sell = key($sorted);
      $gain = $aux_gain;
    }
    unset($sorted[$time]);
  }

  return array(
    'buy' => $buy,
    'sell' => $sell,
    'gain' => $gain,
  );
}

function rbccomp($left_operand, $right_operand) {
  return bccomp($right_operand, $left_operand);
}
