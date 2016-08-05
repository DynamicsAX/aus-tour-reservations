<?php
/**
 * adds new car to the database
 */
//require form class
require_once (plugin_dir_path(__FILE__).'Form.php');

$errors = array();
$success = array();
//save form
if(isset($_POST['name'])){
    include_once (plugin_dir_path(__FILE__).'includes/gump.class.php');
    $is_valid = GUMP::is_valid($_POST, array(
        'name' => 'required|max_len,250'
    ));

    if($is_valid === true) {
        global  $wpdb;
        $table_name =  $wpdb->prefix.'car';
        $wpdb->insert(
            $table_name,
            array('name' => $_POST['name'])
        );
        $success = array('New Car added');
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
//form fields
$fields = array(
    'name' => array(
        'type' => 'text',
        'value' => (($errors) ? $_POST['name'] : '' ),
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
    $form = new Form($formOptions,$fields,$errorsOrSuccess, 'Add New Carsss');
    echo $form;
    return;
}
include_once (plugin_dir_path(__FILE__).'../tables/AusTourCarList.php');


$option = 'per_page';
$args   = [
    'label'   => 'Cars',
    'default' => 5,
    'option'  => 'cars_per_page'
];
$ausTourCarList = new AusTourCarList();
add_screen_option( $option, $args );
?>
    <div class="wrap">
        <h2>Cars</h2>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-car&new=true');  ?>" class="page-title-action"><?php echo L::addnew?></a>
                            <?php
                            $ausTourCarList->prepare_items();
                            $ausTourCarList->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
