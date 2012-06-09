<?php

/**
 * Challenge 8: The demented cloning machine
 *
 * Resources:
 * http://en.wikipedia.org/wiki/Divide_and_conquer_algorithm
 */

ini_set('memory_limit', '512M');

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$people = NULL;
$transformations = array();
foreach ($lines as $line) {
  if (!isset($people)) {
    $people = trim($line);
  }
  else {
    $transformations[] = explode(',', trim($line));
  }
}

foreach ($transformations as $c => $serie) {
  $t = transformations($c, $serie);
  $people = d_and_c($people, $t);
}

echo $people . "\n";
echo md5($people) . "\n";

// $queue = do_magic($people, $transformations);
// echo $queue . "\n";
// echo md5($queue) . "\n";


function d_and_c($people, $transformations) {
  if (strlen($people) <= 1024) {
    list($search, $replace) = $transformations;
    return str_replace($search, $replace, $people);
  }

  echo strlen($people) . "\n";
  $chunks = array_filter(explode("\r\n", chunk_split($people, 1024)));
  foreach ($chunks as $key => $chunk) {
    $chunks[$key] = d_and_c($chunk, $transformations);
  }

  return implode('', $chunks);
}

function do_magic($people, $transformations, $x = NULL, $y = NULL) {
  if (strlen($people) <= 1024) {
    $chunk = '';
    foreach ($transformations as $c => $serie) {
      list($search, $replace) = transformations($x, $y);
      $chunk = str_replace($search, $replace, $chunk);
    }
    return $chunk;
  }
  else {
    $chunks = array_filter(explode("\r\n", chunk_split($people, 1024)));
    foreach ($chunks as $key => $chunk) {
      $chunks[$key] = do_magic($chunk, $transformations);
    }
    return implode('', $chunks);
  }
}

function do_clones($people, $transformations) {
  foreach ($transformations as $c => $serie) {
    list($search, $replace) = transformations($c, $serie);
    echo strlen($people) . "\n";
    $people = str_replace($search, $replace, $people);
  }
  return $people;
}

function transformations($c, $serie) {
  static $cache = array();
  if (!isset($cache[$c])) {
    $search = array();
    $replace = array();
    foreach ($serie as $t) {
      list($a, $b) = explode('=>', $t);
      $search[] = $a;
      $replace[] = $b;
    }
    $cache[$c] = array($search, $replace);
  }
  return $cache[$c];
}
