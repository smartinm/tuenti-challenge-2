<?php

/**
 * Challenge 5: Time is never time again
 */

// Precalculate leds for 1 day
define ('LEDS_DAY_OLD', 2401956);
define ('LEDS_DAY_NEW', 146479);

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
  list($start, $end) = explode(' - ', trim($line));

  $from = date_parse($start);
  $till = date_parse($end);

  $leds_diff = leds_diff($from, $till);
  echo $leds_diff . "\n";
}

function leds_diff($from, $till) {
  $diff_days = daysBetweenDate($from, $till);
  //echo "Diff days: $diff_days\n";

  if ($diff_days == 0 || $from['second'] !== 0 || $from['minute'] !== 0 || $from['hour'] !== 0) {
    $first_day_start = sprintf("2012-08-14 %02d:%02d:%02d", $from['hour'], $from['minute'], $from['second']);
    if ($diff_days > 0) {
      $first_day_end = "2012-08-14 23:59:59";
      $diff_days --;
    }
    else {
      $first_day_end = sprintf("2012-08-14 %02d:%02d:%02d", $till['hour'], $till['minute'], $till['second']);
    }

    $first_time_start = strtotime($first_day_start);
    $first_time_end   = strtotime($first_day_end);

    $first_day_leds = clock_leds($first_time_start, $first_time_end);
  }

  if ($diff_days > 0 && ($till['second'] !== 0 || $till['minute'] !== 0 || $till['hour'] !== 0)) {
    $last_day_start = "2012-08-14 00:00:00";
    $last_day_end = sprintf("2012-08-14 %02d:%02d:%02d", $till['hour'], $till['minute'], $till['second']);

    $last_time_start = strtotime($last_day_start);
    $last_time_end = strtotime($last_day_end);

    $last_day_leds = clock_leds($last_time_start, $last_time_end);
  }

  $leds_diff = 0;
  if (isset($first_day_leds)) {
    $aux =  bcsub($first_day_leds[0], $first_day_leds[1]);
    $leds_diff = bcadd($leds_diff, $aux);
  }
  if (isset($last_day_leds)) {
    $aux =  bcsub($last_day_leds[0], $last_day_leds[1]);
    $leds_diff = bcadd($leds_diff, $aux);
  }
  if ($diff_days > 0) {
    $aux1 = bcmul($diff_days, LEDS_DAY_OLD);
    $aux2 = bcmul($diff_days, LEDS_DAY_NEW);
    $aux =  bcsub($aux1, $aux2);
    $leds_diff = bcadd($leds_diff, $aux);
  }
  return $leds_diff;
}

function clock_leds($time_start, $time_end) {
  $leds_config_old = leds_config_old();
  $leds_config_new = leds_config_new();

	$leds_old = 0;
	$leds_new = start_leds($time_start);
	$prev_mask = time_mask($time_start);

	for ($time = $time_start; $time <= $time_end; $time++) {
		$mask = time_mask($time);

		$time_leds_old = 0;
		$time_leds_new = 0;

		for ($i = 0; $i < 6; $i++) {
		  // Old clock
			$time_leds_old += $leds_config_old[(int) $mask[$i]];

			// New clock
		  if ($prev_mask[$i] != $mask[$i]) {
		    $key = $prev_mask[$i] . '-' . $mask[$i];
		    $time_leds_new += $leds_config_new[$key];
		  }
		}

		$prev_mask = $mask;

		$leds_old = bcadd($leds_old, $time_leds_old);
		$leds_new = bcadd($leds_new, $time_leds_new);
	}

	return array($leds_old, $leds_new);
}

function start_leds($time_start) {
  $leds_config = leds_config_old();
  $mask = time_mask($time_start);
  $leds = 0;
  for ($i = 0; $i < 6; $i++) {
    $leds += $leds_config[(int) $mask[$i]];
  }
  return $leds;
  return 36;
}

function time_mask($time) {
  static $cache = array();
  if (!isset($cache[$time])) {
  	$mask = date('His', $time);
  	$cache[$time] = str_split($mask);
  }
  return $cache[$time];
}

function leds_config_old() {
  return array(
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
}

function leds_config_new() {
  return array(
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
  );
}

function daysBetweenDate($from, $till) {
  $from = gregoriantojd($from['month'], $from['day'], $from['year']);
  $till = gregoriantojd($till['month'], $till['day'], $till['year']);
  return $till - $from;
}
