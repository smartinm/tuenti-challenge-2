<?php

/**
 * Challenge 2: The binary granny
 *
 * Resources:
 * http://www.php.net/manual/en/function.decbin.php#92368
 * http://www.php.net/manual/en/function.bindec.php#92363
 */

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

$num = 1;
foreach ($test_cases as $input) {
  $result = test_case($input);
  echo "Case #$num: $result\n";
  $num ++;
}

function test_case($input) {
  $binary = BCDec2Bin($input);
  $binary_a = str_repeat('1', strlen($binary) - 1);
  $a = BCBin2Dec($binary_a);
  $b = bcsub($input, $a);
  $binary_b = BCDec2Bin($b);
  return substr_count($binary_a, '1') + substr_count($binary_b, '1');
}

// http://www.php.net/manual/en/function.decbin.php#92368
function BCDec2Bin($Input='') {
 $Output='';
 if(preg_match("/^\d+$/",$Input)) {
   while($Input!='0') {
     $Output.=chr(48+($Input{strlen($Input)-1}%2));
     $Input=BCDiv($Input,'2');
   }
   $Output=strrev($Output);
 }
 return(($Output!='')?$Output:'0');
}

// http://www.php.net/manual/en/function.bindec.php#92363
function BCBin2Dec($Input='') {
  $Output='0';
  if(preg_match("/^[01]+$/",$Input)) {
    for($i=0;$i<strlen($Input);$i++)
      $Output=BCAdd(BCMul($Output,'2'),$Input{$i});
  }
  return($Output);
}
