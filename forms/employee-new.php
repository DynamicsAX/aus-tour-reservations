<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Create new Employee
 */

//require form class
require_once (plugin_dir_path(__FILE__).'Form.php');

$errors = array();
$success = array();

global  $wpdb;

//save form
if(isset($_POST['name'])){
    include_once (plugin_dir_path(__FILE__).'includes/gump.class.php');
    $is_valid = GUMP::is_valid($_POST, array(
        'name' => 'required|max_len,100',
        'user' => 'required|integer',
        'role' => 'required|max_len,100',
        'contact' => 'required|max_len,13',
    ));

    if($is_valid === true) {
        $table_name =  $wpdb->prefix.'employee_list';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $_POST['name'],
                'user' => $_POST['user'],
                'role' => $_POST['role'],
                'contact' => $_POST['contact'],
            )
        );
        $success = array('New Employee added');
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
$tableUsers = $wpdb->prefix.'users';
//form fields
$fields = array(
    'name' => array(
        'type' => 'text',
        'value' => (($errors) ? $_POST['name'] : '' ),
        'displayName' => 'Name',
    ),
    'user' => array(
        'type' => 'select',
        'value' => (($errors) ? $_POST['user'] : '' ),
        'displayName' => 'User',
        'options' => $wpdb->get_results( "SELECT ID as id,display_name as name FROM $tableUsers", ARRAY_A ),
    ),
    'role' => array(
        'type' => 'text',
        'value' => (($errors) ? $_POST['role'] : '' ),
        'displayName' => 'Role',
    ),
    'contact' => array(
        'type' => 'text',
        'value' => (($errors) ? $_POST['contact'] : '' ),
        'displayName' => 'Contact(Phone)',
    ),
    'save' => array(
        'type' => 'submit',
    )
);
$errorsOrSuccess = array();
if($errors){
    $errorsOrSuccess['errors']= $errors;
}
if($success){
    $errorsOrSuccess['success']= $success;
}

if(isset($_GET['new'])){
    $form = new Form($formOptions,$fields,$errorsOrSuccess, 'Add New Employee');
    echo $form;
    return;
}
include_once (plugin_dir_path(__FILE__).'../tables/AusTourEmployeeList.php');


$option = 'per_page';
$args   = [
    'label'   => 'Cars',
    'default' => 5,
    'option'  => 'cars_per_page'
];
$ausTourEmployeeList = new AusTourEmployeeList();
add_screen_option( $option, $args );
?>
    <div class="wrap">
        <h2>Employees</h2>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-employee&new=true');  ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'reservation' ); ?></a>
                            <?php
                            $ausTourEmployeeList->prepare_items();
                            $ausTourEmployeeList->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
