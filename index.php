<?php

$pattern = '/ab(?P<foo>cd)ef/';
$string = 'abcdefghi';

preg_match($pattern,$string,$matches);

var_dump($matches);

?>
