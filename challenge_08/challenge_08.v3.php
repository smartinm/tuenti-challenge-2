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
    $transformations[] = explode(',', trim($line));
  }
}

$fp = tmpfile();
fwrite($fp, $people);
fseek($fp, 0);

foreach ($transformations as $serie) {
  $search = array();
  $replace = array();

  foreach ($serie as $t) {
    list($a, $b) = explode('=>', $t);
    $search[] = $a;
    $replace[] = $b;
  }

  $fw = tmpfile();

  while (!feof($fp)) {
    $chunk = fread($fp, 8192);
    $chunk = str_replace($search, $replace, $chunk);
    fwrite($fw, $chunk);
  }

  fclose($fp);
  fseek($fw, 0);
  $fp = $fw;
}

$content = stream_get_contents($fp);
fclose($fp);

echo md5($content) . "\n";
