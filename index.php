<?php

$foo = array();

$foo[0] = ['name' => 'ai', 'age' => '1'];
$foo[1] = ['name' => 'kou', 'age' => '31'];
$foo[2] = ['name' => 'yuka', 'age' => '31'];
?>
<pre>
<?php
foreach($foo as $value) {
  $name[] = $value['name'];
}
print_r($name);
?>
</pre>
