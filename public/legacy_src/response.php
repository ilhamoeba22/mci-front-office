<?php 
$a = array();
$obj1 = new stdClass();
$obj2 = new stdClass();
$obj1->type = 0;
$obj1->content ='My title';
$obj1->bold = 1;
$obj1->align = 0;
$obj1->format = 0;
array_push($a, $obj1);

$obj2->type = 0;
$obj2->content ='This text <br/> two line';
$obj2->bold = 1;
$obj2->align = 0;
array_push($a, $obj2);

echo json_encode($a, JSON_FORCE_OBJECT);
?>