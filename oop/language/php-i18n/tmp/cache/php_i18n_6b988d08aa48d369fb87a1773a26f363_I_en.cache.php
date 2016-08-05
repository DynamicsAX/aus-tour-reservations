<?php class I {
const greeting = 'Hello World!';
const category_somethingother = 'Something other...';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function I($string, $args=NULL) {
    $return = constant("I::".$string);
    return $args ? vsprintf($return,$args) : $return;
}