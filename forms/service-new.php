<?php
/**
 * adds new service to the database
 */
//require form class
require_once (plugin_dir_path(__FILE__).'Form.php');

$errors = array();
$success = array();
global  $wpdb;
$tableService = $wpdb->prefix.'service';
//save form
if(isset($_POST['name'])){
    include_once (plugin_dir_path(__FILE__).'includes/gump.class.php');
    $is_valid = GUMP::is_valid($_POST, array(
        'name' => 'required|max_len,250'
    ));

    if($is_valid === true) {
        $table_name =  $tableService;
        $wpdb->insert(
            $table_name,
            array('name' => $_POST['name'])
        );
        $success = array('New Service added');
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
$values = array(
    'name' =>(($errors) ? $_POST['name'] : '' ),
);
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
    $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tableService WHERE `id`= %s ", $_REQUEST['service']), OBJECT);

//form fields
    $values = array(
        'name' => (($errors) ? $_POST['name'] : $edit->name),
    );
}
//form fields
$fields = array(
    'name' => array(
        'type' => 'text',
        'value' => $values['name'],
        'displayName' => 'Name',
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
    $form = new Form($formOptions,$fields,$errorsOrSuccess, 'Add New Service');
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
include_once (plugin_dir_path(__FILE__).'../tables/AusTourServiceList.php');


$option = 'per_page';
$args   = [
    'label'   => 'Cars',
    'default' => 5,
    'option'  => 'cars_per_page'
];
add_screen_option( $option, $args );
$ausTourServiceList = new AusTourServiceList();
?>
    <div class="wrap">
        <h2>Services</h2>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-service&new=true');  ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'reservation' ); ?></a>
                            <?php
                            $ausTourServiceList->prepare_items();
                            $ausTourServiceList->display();
                            //                            $ausTourCarList->get_column_info();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>