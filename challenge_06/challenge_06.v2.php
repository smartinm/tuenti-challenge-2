<?php

/**
 * Challenge 6: Cross-stitched fonts
 *
 * Resources:
 * http://en.wikipedia.org/wiki/Word_wrap
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
      'message' => $lines[$i+1],
    );
    $i++;
  }
}

$num = 1;
foreach ($test_cases as $input) {
  list($width, $height, $ct) = explode(' ', trim($input['info']));
  $message = trim($input['message']);

  $result = test_case($width, $height, $ct, $message);
  echo "Case #$num: $result\n";
  $num ++;
}

function test_case($width, $height, $ct, $message) {
  $words = explode(' ', $message);

  $width_px = $width * $ct;
  //echo "width_px: $width_px\n";
  $height_px = $height * $ct;
  //echo "height_px: $height_px\n";

  $max_font_size = max_font_size($width_px, $words);
  $font_size = $max_font_size;
  while ($font_size > 0) {
    //echo "testing: $font_size\n";
    if (word_wrap($width_px, $height_px, $words, $font_size)) {
      break;
    }
    $font_size --;
  }

  $characters = array_sum(array_map('strlen', $words));
  //echo "characters: $characters\n";
  return ceil(((pow($font_size, 2) / 2) * $characters) / $ct);
}

function max_font_size($width, $words) {
  $lengths = array_map('strlen', $words);
  $max_length = max($lengths);
  return floor($width / $max_length);
}

function word_wrap($width, $height, $words, $font_size) {
  $width_left = $width;
  $prefix_space = FALSE;
  $lines = 1;
  foreach ($words as $word) {
    if ((width($word, $font_size, $prefix_space)) > $width_left) {
      if (bcmul($lines + 1, $font_size) > $height) {
        return FALSE;
      }
      $width_left = bcsub($width, width($word, $font_size, FALSE));
      $prefix_space = FALSE;
      $lines ++;
    }
    else {
      $width_left = bcsub($width_left, width($word, $font_size, $prefix_space));
      $prefix_space = TRUE;
    }
  }
  return bcmul($lines, $font_size) <= $height;
}

function width($word, $font_size, $add_space) {
  $len = strlen($word);
  $width = bcmul($len, $font_size);
  return $add_space ? bcadd($width, $font_size) : $width;
}
