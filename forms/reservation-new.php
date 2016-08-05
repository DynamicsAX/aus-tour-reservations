<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Create new Reservations
 */

//require form class
require_once(plugin_dir_path(__FILE__) . 'Form.php');

$errors = array();
$success = array();


global $wpdb;
//save form
if (isset($_POST['employeeId'])) {
    include_once(plugin_dir_path(__FILE__) . 'includes/gump.class.php');
    $is_valid = GUMP::is_valid($_POST, array(
        'employeeId' => 'required|integer|max_len,9',
        'date' => 'required|date|max_len,100',
        'timeStart' => 'required|max_len,100',
        'timeEnd' => 'required|max_len,100',
        'hours' => 'required|numeric|max_len,5',
        'route' => 'required|max_len,1024',
        'reservationTitle' => 'required|max_len,128',
        'PAX' => 'required|integer|max_len,5',
        'carId' => 'required|integer',
        'serviceId' => 'required|integer',
        'amount' => 'required|numeric',
        'amountCash' => 'numeric',
    ));

    if ($is_valid === true) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ausTourReservations';
        //check if it's an edit or an update
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {

            $wpdb->update(
                $table_name,
                array(
                    'employeeId' => $_POST['employeeId'],
                    'date' => $_POST['date'],
                    'timeStart' => $_POST['timeStart'],
                    'timeEnd' => $_POST['timeEnd'],
                    'hours' => $_POST['hours'],
                    'route' => $_POST['route'],
                    'reservationTitle' => $_POST['reservationTitle'],
                    'PAX' => $_POST['PAX'],
                    'carId' => $_POST['carId'],
                    'serviceId' => $_POST['serviceId'],
                    'comment' => $_POST['comment'],
                    'amount' => $_POST['amount'],
                    'amountCash' => $_POST['amountCash'],
                ),
                array('id' => $_REQUEST['reservation'])
            );
            $success = array('New Reservation updated');
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'employeeId' => $_POST['employeeId'],
                    'date' => $_POST['date'],
                    'timeStart' => $_POST['timeStart'],
                    'timeEnd' => $_POST['timeEnd'],
                    'hours' => $_POST['hours'],
                    'route' => $_POST['route'],
                    'reservationTitle' => $_POST['reservationTitle'],
                    'PAX' => $_POST['PAX'],
                    'carId' => $_POST['carId'],
                    'serviceId' => $_POST['serviceId'],
                    'comment' => $_POST['comment'],
                    'amount' => $_POST['amount'],
                    'amountCash' => $_POST['amountCash'],
                )
            );
            $success = array('New Reservation added');
        }
    } else {
        $errors = $is_valid;
    }
}

//form options
$formOptions = array(
    'name' => 'form',
    'class' => '',
    'method' => 'post',
);
$table_employee_list = $wpdb->prefix . 'employee_list';
$table_service = $wpdb->prefix . 'service';
$table_car = $wpdb->prefix . 'car';
$table_reservations = $wpdb->prefix . 'ausTourReservations';
//create a default array and have it with the default values with the name fields

$fields = array(
    'employeeId' => (($errors) ? $_POST['employeeId'] : ''),
    'date' => (($errors) ? $_POST['date'] : ''),
    'timeStart' => (($errors) ? $_POST['timeStart'] : ''),
    'timeEnd' => (($errors) ? $_POST['timeEnd'] : ''),
    'hours' => (($errors) ? $_POST['hours'] : ''),
    'route' => (($errors) ? $_POST['route'] : ''),
    'reservationTitle' => (($errors) ? $_POST['reservationTitle'] : ''),
    'PAX' => (($errors) ? $_POST['PAX'] : ''),
    'carId' => (($errors) ? $_POST['carId'] : ''),
    'serviceId' => (($errors) ? $_POST['serviceId'] : ''),
    'comment' => (($errors) ? $_POST['comment'] : ''),
    'amount' => (($errors) ? $_POST['amount'] : ''),
    'amountCash' => (($errors) ? $_POST['amountCash'] : ''),
);

//if there is an edit action get the values from database
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
    $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reservations WHERE `id`= %s ", $_REQUEST['reservation']), OBJECT);
    $fields = array(
        'employeeId' => (($errors) ? $_POST['employeeId'] : $edit->employeeId),
        'date' => (($errors) ? $_POST['date'] : $edit->date),
        'timeStart' => (($errors) ? $_POST['timeStart'] : $edit->timeStart),
        'timeEnd' => (($errors) ? $_POST['timeEnd'] : $edit->timeEnd),
        'hours' => (($errors) ? $_POST['hours'] : $edit->hours),
        'route' => (($errors) ? $_POST['route'] : $edit->route),
        'reservationTitle' => (($errors) ? $_POST['reservationTitle'] : $edit->reservationTitle),
        'PAX' => (($errors) ? $_POST['PAX'] : $edit->PAX),
        'carId' => (($errors) ? $_POST['carId'] : $edit->carId),
        'serviceId' => (($errors) ? $_POST['serviceId'] : $edit->serviceId),
        'comment' => (($errors) ? $_POST['comment'] : $edit->comment),
        'amount' => (($errors) ? $_POST['amount'] : $edit->amount),
        'amountCash' => (($errors) ? $_POST['amountCash'] : $edit->amountCash),
    );
}
//form fields
$fields = array(
    'employeeId' => array(
        'type' => 'select',
        'value' => $fields['employeeId'],
        'displayName' => 'Employee ID',
        'options' => $wpdb->get_results("SELECT id,name FROM $table_employee_list", ARRAY_A),
    ),
    'date' => array(
        'type' => 'text',
        'value' => $fields['date'],
        'displayName' => 'Date',
        'class' => 'datePicker',
    ),
    'serviceId' => array(
        'type' => 'select',
        'value' => $fields['serviceId'],
        'displayName' => 'Service ID',

        'options' => $wpdb->get_results("SELECT id,name FROM $table_service", ARRAY_A),
    ),
    'timeStart' => array(
        'type' => 'text',
        'value' => $fields['timeStart'],
        'displayName' => 'Start Time',
        'class' => 'timePicker',
        'id' => 'start_time',
        'readonly' => '',
    ),
    'timeEnd' => array(
        'type' => 'text',
        'value' => $fields['timeEnd'],
        'displayName' => 'End Time',
        'class' => 'timePicker',
        'id' => 'end_time',
        'readonly' => '',
    ),
    'hours' => array(
        'type' => 'text',
        'value' => $fields['hours'],
        'displayName' => 'Hours',
        'id' => 'hours',
        'readonly' => 'readonly',
    ),
    'route' => array(
        'type' => 'text',
        'value' => $fields['route'],
        'displayName' => 'Route',
    ),
    'reservationTitle' => array(
        'type' => 'text',
        'value' => $fields['reservationTitle'],
        'displayName' => 'Reservation Title',
    ),
    'PAX' => array(
        'type' => 'text',
        'value' => $fields['PAX'],
        'displayName' => 'PAX',
    ),
    'carId' => array(
        'type' => 'select',
        'value' => $fields['carId'],
        'displayName' => 'Car ID',
        'options' => $wpdb->get_results("SELECT id,name FROM $table_car", ARRAY_A),
    ),
    'comment' => array(
        'type' => 'textarea',
        'value' => $fields['comment'],
        'displayName' => 'Comment',
    ),
    'amount' => array(
        'type' => 'text',
        'value' => $fields['amount'],
        'displayName' => 'Amount',
    ),
    'amountCash' => array(
        'type' => 'text',
        'value' => $fields['amountCash'],
        'displayName' => 'Amount Cash',
    ),
    'save' => array(
        'type' => 'submit',
    )
);
$errorsOrSuccess = array();
if ($errors) {
    $errorsOrSuccess['errors'] = $errors;
}
if ($success) {
    $errorsOrSuccess['success'] = $success;
}

if (isset($_GET['new'])) {
    $form = new Form($formOptions, $fields, $errorsOrSuccess, 'Add Reservation');
    echo $form;
    return;
}
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'edit' :
            $form = new Form($formOptions, $fields, $errorsOrSuccess, 'Edit reservation');
            echo $form;
    }

    return;
}

$option = 'per_page';
$args = [
    'label' => 'Cars',
    'default' => 5,
    'option' => 'cars_per_page'
];
add_screen_option($option, $args);
include_once(plugin_dir_path(__FILE__) . '../tables/AusTourReservationList.php');
$ausTourReservationList = new AusTourReservationList();


?>
<div class="wrap">
    <h2><?php _e('AustriaTour Reservation Management', 'reserv'); ?></h2>

    <div id="poststuff">
        <div class="meta-box-sortables ui-sortable">
            <form method="post">
                <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-reservations&new=true'); ?>"
                   class="page-title-action"><?php echo esc_html_x('Add New', 'reservation'); ?></a>
                <?php
                $ausTourReservationList->prepare_items();
                $ausTourReservationList->display();
                ?>
                <div class="totals" style="float: right;">
                    Total: <?= $ausTourReservationList::$total ?><br>
                    Total Amount Cash: <?= $ausTourReservationList::$totalCash ?>
                </div>
            </form>
        </div>
        <br class="clear">
    </div>
</div>
