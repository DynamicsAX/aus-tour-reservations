<?php class L {
const greeting = 'Hello World!';
const cars = 'cars';
const car = 'car';
const name = 'name';
const addnew = 'Add New';
const newStr = 'new';
const added = 'added';
const updated = 'updated';
const first = 'first';
const reservation = 'reservation';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}