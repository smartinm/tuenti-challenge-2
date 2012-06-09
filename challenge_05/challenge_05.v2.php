<?php

/**
 * Challenge 5: Time is never time again
 *
 * Resources:
 * http://stackoverflow.com/questions/5988450/difference-between-2-dates-in-seconds
 * https://github.com/ricardclau/tuenti-contest/blob/master/2011/test6.php
 */

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
  list($start, $end) = explode(' - ', trim($line));
  $diff = time_diff($start, $end);
  $leds_diff = leds_diff($diff);
  echo $leds_diff . "\n";
}

function time_diff($start, $end) {
  $time_start = strtotime($start);
  $time_end = strtotime($end);
  return $time_end - $time_start;
}

function leds_diff($diff) {
  $old_ledsconfig = array(
    0 => 6,
    1 => 2,
    2 => 5,
    3 => 5,
    4 => 4,
    5 => 5,
    6 => 6,
    7 => 3,
    8 => 7,
    9 => 6,
  );
  $new_ledsconfig = array(
     '0-1' => 0,
     '1-2' => 4,
     '2-3' => 1,
     '3-4' => 1,
     '4-5' => 2,
     '5-6' => 1,
     '6-7' => 1,
     '7-8' => 4,
     '8-9' => 0,
     '9-0' => 1,
     '5-0' => 2, // 59m or 59s -> 00
     '2-0' => 2, // 23h -> 00
     '3-0' => 2,
  );

	$old_leds = 0;
	$new_leds = 36;
	$antmask = array_fill(0, 6, '0');
	for ($i = 0; $i <= $diff; $i++) {
		$mask = gmdate('His', $i);
		$maskarr = str_split($mask);

		$old_tmpleds = 0;
		$new_tmpleds = 0;
		for($k = 0; $k < 6; $k++) {
		  if($antmask[$k] != $maskarr[$k]) {
		    $new_tmpleds += $new_ledsconfig[$antmask[$k].'-'.$maskarr[$k]];
		  }
			$old_tmpleds += $old_ledsconfig[(int)$maskarr[$k]];
		}
		$antmask = $maskarr;
		$old_leds += $old_tmpleds;
		$new_leds += $new_tmpleds;
	}

	echo $old_leds . " - " . $new_leds . "\n";
	return $old_leds - $new_leds;
}
