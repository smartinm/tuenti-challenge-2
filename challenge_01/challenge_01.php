<?php

/**
 * Challenge 1: The cell phone keypad
 *
 * Resources:
 * http://en.wikipedia.org/wiki/A*_search_algorithm
 * http://stackoverflow.com/questions/5112111/a-search-algorithm-in-php
 */

define('TIME_MOVE_VERTICAL', 300);
define('TIME_MOVE_HORIZONTAL', 200);
define('TIME_MOVE_DIAGONAL', 350);
define('TIME_PRESS_BUTTON', 100);
define('TIME_WAIT_SAME_BUTTON', 500);
define('KEY_PAD_WIDTH', 3);
define('KEY_PAD_HEIGHT', 4);

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$test_cases = array();
$count = NULL;
foreach($lines as $line) {
  if (!isset($count)) {
    $count = intval($line);
  }
  else if (count($test_cases) < $count) {
    $test_cases[] = $line;
  }
}

foreach ($test_cases as $input) {
  $total_time = test_case($input);
  echo $total_time . "\n";
}

function test_case($input) {
  $caps_lock = FALSE;
  $total_time = 0;

  $input = str_replace(' ', '_', $input);
  $text = str_split($input);
  $text = process_caps($text);

  $key = '0';
  for ($i = 0; $i < count($text); $i++) {
    $next_key = $text[$i];
    //echo "Key: $next_key\n";

    if (is_same_button($key, $next_key)) {
      if ($i > 0) {
        $time = TIME_WAIT_SAME_BUTTON;
        $total_time += $time;
        //echo "Wait press same button: $time\n";
      }
    }
    else {
      $time = move_finger($key, $next_key);
      $total_time += $time;
      //echo "Move finger: $time\n";
    }

    $time = press_button($next_key);
    //echo "Press button: $time\n";

    $total_time += $time;
    $key = $next_key;
  }

  return $total_time;
}

function key_map($key) {
  static $key_map = NULL;

  if (!isset($key_map)) {
    $key_pad = array(
      array(   '_1', 'ABC2',  'DEF3'),
      array( 'GHI4', 'JKL5',  'MNO6'),
      array('PQRS7', 'TUV8', 'WXYZ9'),
      array(   NULL,    '0',     '^'),
    );

    $key_map = array();
    foreach ($key_pad as $y => $row) {
      foreach ($row as $x => $keys) {
        $index = $y * KEY_PAD_WIDTH + $x;
        foreach (str_split($keys) as $pos => $k) {
          if (isset($k)) {
            $key_map[$k] = array('index' => $index, 'position' => $pos);
          }
        }
      }
    }
  }

  $key = strtoupper($key);
  return isset($key_map[$key]) ? $key_map[$key] : FALSE;
}

function press_button($key) {
  $key_info = key_map($key);
  return TIME_PRESS_BUTTON * ($key_info['position'] + 1);
}

function is_same_button($current_key, $next_key) {
  $current_key_info = key_map($current_key);
  $next_key_info = key_map($next_key);
  return ($current_key_info['index'] === $next_key_info['index']);
}

function move_finger($current_key, $next_key) {
  $current_key_info = key_map($current_key);
  $next_key_info = key_map($next_key);
  return time_move_finger($current_key_info['index'], $next_key_info['index']);
}

function time_move_finger($start, $target) {
  return a_star($start, $target, 'neighbors', 'heuristic');
}

function process_caps($text) {
  $caps_lock = FALSE;
  $text_with_caps = array();
  $key = '0';
  for ($i = 0; $i < count($text); $i++) {
    $next_key = $text[$i];
    if (ctype_alpha($next_key)) {
      if ((ctype_upper($next_key) && !$caps_lock) || (ctype_lower($next_key) && $caps_lock)) {
        $text_with_caps[] = '^';
        $caps_lock = !$caps_lock;
      }
      $text_with_caps[] = $next_key;
    }
    else {
      // TODO
      $text_with_caps[] = $next_key;
    }

    $key = $next_key;
  }
  return $text_with_caps;
}

///////////////////////////////////////////////////////////////////////////////
// A-star algorithm:
//   $start, $target - node indexes
//   $neighbors($i)     - map of neighbor index => step cost
//   $heuristic($i, $j) - minimum cost between $i and $j

function a_star($start, $target, $neighbors, $heuristic) {
  $open_heap = array($start); // binary min-heap of indexes with values in $f
  $open      = array($start => TRUE); // set of indexes
  $closed    = array();               // set of indexes

  $g[$start] = 0;
  $h[$start] = $heuristic($start, $target);
  $f[$start] = $g[$start] + $h[$start];

  while ($open) {
    $i = heap_pop($open_heap, $f);
    unset($open[$i]);
    $closed[$i] = TRUE;

    if ($i == $target) {
      $path = array();
      for (; $i != $start; $i = $from[$i])
        $path[] = $i;

      return $g[$path[0]];
      //return array_reverse($path);
    }

    foreach ($neighbors($i) as $j => $step)
      if (!array_key_exists($j, $closed))
      if (!array_key_exists($j, $open) || $g[$i] + $step < $g[$j]) {
      $g[$j] = $g[$i] + $step;
      $h[$j] = $heuristic($j, $target);
      $f[$j] = $g[$j] + $h[$j];
      $from[$j] = $i;

      if (!array_key_exists($j, $open)) {
        $open[$j] = TRUE;
        heap_push($open_heap, $f, $j);
      } else
        heap_raise($open_heap, $f, $j);
    }
  }

  return FALSE;
}

function heap_float(&$heap, &$values, $i, $index) {
  for (; $i; $i = $j) {
    $j = ($i + $i%2)/2 - 1;
    if ($values[$heap[$j]] < $values[$index])
      break;
    $heap[$i] = $heap[$j];
  }
  $heap[$i] = $index;
}

function heap_push(&$heap, &$values, $index) {
  heap_float($heap, $values, count($heap), $index);
}

function heap_raise(&$heap, &$values, $index) {
  heap_float($heap, $values, array_search($index, $heap), $index);
}

function heap_pop(&$heap, &$values) {
  $front = $heap[0];
  $index = array_pop($heap);
  $n = count($heap);
  if ($n) {
    for ($i = 0;; $i = $j) {
      $j = $i*2 + 1;
      if ($j >= $n)
        break;
      if ($j+1 < $n && $values[$heap[$j+1]] < $values[$heap[$j]])
        ++$j;
      if ($values[$index] < $values[$heap[$j]])
        break;
      $heap[$i] = $heap[$j];
    }
    $heap[$i] = $index;
  }
  return $front;
}

function node($x, $y) {
  return $y * KEY_PAD_WIDTH + $x;
}

function coord($i) {
  $x = $i % KEY_PAD_WIDTH;
  $y = ($i - $x) / KEY_PAD_WIDTH;
  return array($x, $y);
}

function neighbors($i) {
  $neighbors = array();
  list ($x, $y) = coord($i);

  if ($x-1 >= 0) $neighbors[node($x-1, $y)] = TIME_MOVE_HORIZONTAL;
  if ($x+1 < KEY_PAD_WIDTH) $neighbors[node($x+1, $y)] = TIME_MOVE_HORIZONTAL;
  if ($y-1 >= 0) $neighbors[node($x, $y-1)] = TIME_MOVE_VERTICAL;
  if ($y+1 < KEY_PAD_HEIGHT) $neighbors[node($x, $y+1)] = TIME_MOVE_VERTICAL;

  if ($x-1 >= 0 && $y-1 >= 0) $neighbors[node($x-1, $y-1)] = TIME_MOVE_DIAGONAL;
  if ($x-1 >= 0 && $y+1 < KEY_PAD_HEIGHT) $neighbors[node($x-1, $y+1)] = TIME_MOVE_DIAGONAL;
  if ($x+1 < KEY_PAD_WIDTH && $y-1 >= 0) $neighbors[node($x+1, $y-1)] = TIME_MOVE_DIAGONAL;
  if ($x+1 < KEY_PAD_WIDTH && $y+1 < KEY_PAD_HEIGHT) $neighbors[node($x+1, $y+1)] = TIME_MOVE_DIAGONAL;

  unset($neighbors[9]);

  return $neighbors;
}

function heuristic($i, $j) {
  list ($i_x, $i_y) = coord($i);
  list ($j_x, $j_y) = coord($j);
  return abs($i_x - $j_x) + abs($i_y - $j_y);
}
