<?php

/**
 * Challenge 9: Il nomme della magnolia
 *
 * Requisites:
 * - MySQL Server
 * - DB schema: challenge_09.sql
 * - DB init script: challenge_09_db_init.php
 *
 * Resources:
 * http://en.wikipedia.org/wiki/Inverted_index
 */

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'challenge_9');

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$test_cases = array();
$count = NULL;
foreach($lines as $line) {
  if (!isset($count)) {
    $count = intval($line);
  }
  else if (count($test_cases) < $count) {
    $test_cases[] = trim($line);
  }
}

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS)
  or die('Could not connect: ' . mysql_error());

mysql_select_db(DB_NAME)
  or die('Could not select database');

foreach ($test_cases as $input) {
  list($word, $n) = explode(' ', $input);
  $result = test_case($word, $n);
  echo "$result[0]-$result[1]-$result[2]\n";
}

mysql_close($link);

function test_case($word, $n) {
  $info = array(0,0,0);
  $offset = $n - 1;
  $sql = sprintf("SELECT document, line, position FROM secret_table WHERE word = '%s' LIMIT %s,1",
      mysql_real_escape_string($word), $offset);
  if ($result = mysql_query($sql)) {
    $info = mysql_fetch_array($result);
    mysql_free_result($result);
  }
  return $info;
}
