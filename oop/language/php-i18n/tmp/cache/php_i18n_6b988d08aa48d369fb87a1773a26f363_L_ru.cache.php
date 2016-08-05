<?php class L {
const greeting = 'Hello World!';
const cars = 'Машины';
const car = 'Машина';
const name = 'Наименование';
const addnew = 'Добавить';
const newStr = 'new';
const added = 'Добавлено';
const updated = 'Обновлено';
const first = 'первый';
const reservation = 'бронь';
const edit = 'Редактироать';
const employees = 'Сотрудники';
const employee = 'Сотрудник';
const user = 'Пользователь';
const nameUser = 'Имя';
const role = 'Должность';
const phone = 'Телефон';
const save = 'Сохранить';
const newRecord = 'Новая запись';
const editRecord = 'Редактирование';
const idnumber = 'Номер';
const delete = 'Удалить';
const services = 'Услуги';
const reservationSystem = 'AustriaTour управление резервированием';
const total = 'Итого';
const totalCash = 'Итого нал.';
const from = 'С';
const to = 'по';
const select = 'Выбрать';
const filter = 'Фильтр';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}