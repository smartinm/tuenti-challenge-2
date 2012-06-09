<?php

/**
 * Challenge 9: database init.
 *
 * http://dev.mysql.com/doc/refman/5.0/en/insert-speed.html
 */

define('DOCUMENTS_FOLDER', 'documents');

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'challenge_9');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS)
  or die('Could not connect: ' . mysql_error());

mysql_select_db(DB_NAME)
  or die('Could not select database');

mysql_query("LOCK TABLES secret_table WRITE");

$dir = './' . DOCUMENTS_FOLDER;
$filenames = scandir($dir);
foreach ($filenames as $filename) {
  if ($filename == '.' || $filename == '..') {
    continue;
  }

  $n_doc = intval($filename);
  echo "Processing $n_doc document\n";

  $lines = file($dir . '/' . $filename);
  for ($n_line = 0; $n_line < count($lines); $n_line ++) {
    $line = $lines[$n_line];

    $line = trim($line);
    if (!empty($line)) {
      $words = preg_split('/\s/', $line);
      for ($n_pos = 0; $n_pos < count($words); $n_pos ++) {
        db_insert($words[$n_pos], $n_doc, $n_line + 1, $n_pos + 1);
      }
    }
  }
}

mysql_query("UNLOCK TABLES");

mysql_close($link);

function db_insert($word, $doc, $line, $pos) {
  mysql_query("INSERT INTO secret_table (id, word, line, position, document) VALUES(NULL, '$word', $line, $pos, $doc)");
}
