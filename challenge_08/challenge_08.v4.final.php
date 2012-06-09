<?php

/**
 * Challenge 8: The demented cloning machine
 */

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$people = NULL;
$transformations = array();
foreach ($lines as $line) {
  if (!isset($people)) {
    $people = trim($line);
  }
  else {
    $transformations[] = transformation(trim($line));
  }
}

$replace = array();
$indexes = array();
foreach ($transformations as $index => $serie) {
  foreach ($serie as $person => $clones) {
    if (!isset($replace[$person])) {
      $replace[$person] = $clones;
      $indexes[$person] = $index;
    }
  }
}

foreach ($replace as $person => $clones) {
  $aux = $clones;
  $index = $indexes[$person] + 1;
  for ($i = $index; $i < count($transformations); $i ++) {
    $aux = strtr($aux, $transformations[$i]);
  }
  $replace[$person] = $aux;
}

$ctx = hash_init('md5');
foreach (str_split($people) as $person) {
  $buffer = isset($replace[$person]) ? $replace[$person] : $person;
  hash_update($ctx, $buffer);
}
$md5_sum = hash_final($ctx);
echo $md5_sum . "\n";

function transformation($input) {
  $transformation = array();
  foreach (explode(',', $input) as $t) {
    list($x, $y) = explode('=>', $t);
    $transformation[$x] = $y;
  }
  return $transformation;
}
