<?php

/**
 * Challenge 8: The demented cloning machine
 *
 * Resources:
 * http://www.simplemachines.org/community/index.php?topic=175031.0
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

$queue = do_magic($people, $transformations);
echo $queue . "\n";
echo md5($queue) . "\n";

function do_magic($people, $transformations) {
  $search = array();
  $replace = array();

  $chunks = array_filter(explode("\r\n", chunk_split($people)));
  foreach ($chunks as $key => $chunk) {
    foreach ($transformations as $c => $serie) {
      list($search, $replace) = transformations($c, $serie);
      echo strlen($chunk) . "\n";
      $chunk = str_replace($search, $replace, $chunk);
    }
    //$chunks[$key] = $chunk;
  }

  return implode('', $chunks);
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
