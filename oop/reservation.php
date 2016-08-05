<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/22/16
 * Time: 11:24 AM
 */
require_once ('services.php');

class reservation extends services
{
    public function showTable()
    {

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
            <h2><?php echo L::reservationSystem ?></h2>

            <div id="poststuff">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-reservations&new=true'); ?>"
                           class="page-title-action"><?php echo L::addnew?></a>
                        <?php
                        $ausTourReservationList->prepare_items();
                        $ausTourReservationList->display();
                        ?>
                        <div class="totals" style="float: right;">
                            <?php echo L::total?>: <?= $ausTourReservationList::$total ?><br>
                            <?php echo L::totalCash?>: <?= $ausTourReservationList::$totalCash ?>
                        </div>
                    </form>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }
    public function fieldsAndValues()
    {   global $wpdb;

        $errors = $this->errors;
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
            $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE `id`= %s ", $_REQUEST['reservation']), OBJECT);
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
        return $this->createFields($fields);
    }
    public function createFields($values)
    {   global $wpdb;

        $table_employee_list = $wpdb->prefix . 'employee_list';
        $table_service = $wpdb->prefix . 'service';
        $table_car = $wpdb->prefix . 'car';
        $fields = array(
            'employeeId' => array(
                'type' => 'select',
                'value' => $values['employeeId'],
                'displayName' => 'Employee ID',
                'options' => $wpdb->get_results("SELECT id,name FROM $table_employee_list", ARRAY_A),
            ),
            'date' => array(
                'type' => 'text',
                'value' => $values['date'],
                'displayName' => 'Date',
                'class' => 'datePicker',
            ),
            'serviceId' => array(
                'type' => 'select',
                'value' => $values['serviceId'],
                'displayName' => 'Service ID',

                'options' => $wpdb->get_results("SELECT id,name FROM $table_service", ARRAY_A),
            ),
            'timeStart' => array(
                'type' => 'text',
                'value' => $values['timeStart'],
                'displayName' => 'Start Time',
                'class' => 'timePicker',
                'id' => 'start_time',
                'readonly' => '',
            ),
            'timeEnd' => array(
                'type' => 'text',
                'value' => $values['timeEnd'],
                'displayName' => 'End Time',
                'class' => 'timePicker',
                'id' => 'end_time',
                'readonly' => '',
            ),
            'hours' => array(
                'type' => 'text',
                'value' => $values['hours'],
                'displayName' => 'Hours',
                'id' => 'hours',
                'readonly' => 'readonly',
            ),
            'route' => array(
                'type' => 'text',
                'value' => $values['route'],
                'displayName' => 'Route',
            ),
            'reservationTitle' => array(
                'type' => 'text',
                'value' => $values['reservationTitle'],
                'displayName' => 'Reservation Title',
            ),
            'PAX' => array(
                'type' => 'text',
                'value' => $values['PAX'],
                'displayName' => 'PAX',
            ),
            'carId' => array(
                'type' => 'select',
                'value' => $values['carId'],
                'displayName' => 'Car ID',
                'options' => $wpdb->get_results("SELECT id,name FROM $table_car", ARRAY_A),
            ),
            'comment' => array(
                'type' => 'textarea',
                'value' => $values['comment'],
                'displayName' => 'Comment',
            ),
            'amount' => array(
                'type' => 'text',
                'value' => $values['amount'],
                'displayName' => 'Amount',
            ),
            'amountCash' => array(
                'type' => 'text',
                'value' => $values['amountCash'],
                'displayName' => 'Amount Cash',
            ),
            'save' => array(
                'type' => 'submit',
            )
        );
        return $fields;
    }
    public function init()
    {   global $wpdb;
        $this->tableName = $wpdb->prefix . 'ausTourReservations';
        $this->serviceName = 'reservation';
        $this->fieldCheck = 'employeeId';
    }
    public function validate()
    {
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
        return $is_valid;
    }

    public function update($id)
    { 
        global $wpdb;
        $wpdb->update(
            $this->tableName,
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
            array('id' => $id)
        );
        $this->success =  array('New Reservation updated');
    }


    public function insert()
    {   global $wpdb;
        $wpdb->insert(
            $this->tableName,
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
        $this->success = array('New Reservation added');
    }
}

new reservation();