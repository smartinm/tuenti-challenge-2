<?php

/**
 * Challenge 7: The "secure" password
 *
 * Resources:
 * http://en.wikipedia.org/wiki/Topological_sorting
 */

$lines = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$input = array();
foreach ($lines as $line) {
  $input[] = trim($line);
}

if (count($input)) {
  $passwords = secure_password($input);
  foreach ($passwords as $password) {
    echo $password . "\n";
  }
}

function secure_password($subcodes) {
  $next = array();
  $prev = array();

  foreach ($subcodes as $subcode) {
    $subcode = str_split($subcode);
    for ($i = 0; $i < count($subcode) - 1; $i++) {
      $c = $subcode[$i];
      $n = $subcode[$i+1];

      $next[$c][$n] = $n;
      $prev[$n][$c] = $c;
    }
  }

  foreach ($next as $c => $chars) {
    foreach ($chars as $n) {
      if (count(array_intersect($prev[$n], $chars))) {
        unset($next[$c][$n]);
      }
    }
  }

  foreach ($prev as $c => $chars) {
    foreach ($chars as $n) {
      if (!in_array($c, $next[$n])) {
        unset($prev[$c][$n]);
      }
    }
  }

  $init_nodes = array_values(array_diff(array_keys($next), array_keys($prev)));
  $passwords = topological_sorting($init_nodes, $next, $prev);
  sort($passwords);
  return $passwords;
}


function topological_sorting($init_nodes, $graph, $incoming) {
  $result = array();

  $index = 0;
  $stack[] = array($init_nodes, array(), $incoming);
  while (count($stack) > 0) {
    list($nodes, $list, $edges) = array_shift($stack);
    $processed[implode('', $nodes)] = 1;

    while (count($nodes) > 0) {
      $n = array_shift($nodes);

      $tmp = $nodes;
      $tmp_list = $list;
      array_push($tmp, $n);
      if (empty($processed[implode('', $tmp)])) {
        $stack[] = array($tmp, $tmp_list, $edges);
      }

      $list[$n] = $n;
      if (isset($graph[$n])) {
        foreach ($graph[$n] as $m) {
          unset($edges[$m][$n]);
          if (empty($edges[$m])) {
            $nodes[$m] = $m;
          }
        }
      }
    }

    $code = implode('', $list);
    $result[$code] = $code;
  }

  return $result;
}
