<?php

function reserv($fields, $errors) {
  // Check args and replace if necessary
  if (!is_array($fields))     $fields = array();
  if (!is_wp_error($errors))  $errors = new WP_Error;

  // Check for form submit
  if (isset($_POST['submit'])) {

    // Get fields from submitted form
    $fields = reserv_get_fields();

    // Validate fields and produce errors
    if (reserv_validate($fields, $errors)) {

      // If successful, create reservation
      insert_reservation($fields);

      // And display a message
      echo 'Reservation complete.';

      // Clear field data
      $fields = array();
    }
  }

  // Santitize fields
  reserv_sanitize($fields);
  // Generate form
  reserv_display_form($fields, $errors);
}
function insert_reservation($fields){
  global  $wpdb;
  /*$table_name = $wpdb->prefix.'ausTourReservations';
  $wpdb->insert(
      $table_name,
      array('reservationTitle' => $fields['title'])
  );*/
};
function reserv_sanitize(&$fields) {
  $fields['title']   =  isset($fields['title'])  ? sanitize_user($fields['title']) : '';
}
function reserv_display_form($fields = array(), $errors = null) {

  // Check for wp error obj and see if it has any errors
  if (is_wp_error($errors) && count($errors->get_error_messages()) > 0) {

    // Display errors
    ?><ul><?php
    foreach ($errors->get_error_messages() as $key => $val) {
      ?><li>
      <?php echo $val; ?>
      </li><?php
    }
    ?></ul><?php
  }

  // Disaply form

  ?><form action="<?php $_SERVER['REQUEST_URI'] ?>" method="post">
  <div>
    <label for="title">Title <strong>*</strong></label>
    <input type="text" name="title" value="<?php echo (isset($fields['title']) ? $fields['title'] : '') ?>">
  </div>

  

  <input type="submit" name="submit" value="Register">
  </form><?php
}
function reserv_get_fields() {
  return array(
      'title'   =>  isset($_POST['title'])   ?  $_POST['title']   :  '',
  );
}
function reserv_validate(&$fields, &$errors) {

  // Make sure there is a proper wp error obj
  // If not, make one
  if (!is_wp_error($errors))  $errors = new WP_Error;

  // Validate form data

  if (empty($fields['title'])){
    $errors->add('field', 'Required form field is missing');
  }
  if (strlen($fields['title']) < 4) {
    $errors->add('title_length', 'Username too short. At least 4 characters is required');
  }

  // If errors were produced, fail
  if (count($errors->get_error_messages()) > 0) {
    return false;
  }

  // Else, success!
  return true;
}
function fs_get_wp_config_path()
{
  $base = dirname(__FILE__);
  $path = false;

  if (@file_exists(dirname(dirname($base))."/wp-config.php"))
  {
    $path = dirname(dirname($base))."/wp-config.php";
  }
  else
    if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
    {
      $path = dirname(dirname(dirname($base)));
    }
    else
      $path = false;

  if ($path != false)
  {
    $path = str_replace("\\", "/", $path);
  }
  return $path;
}
// The callback function for the [reserv] shortcode
function reserv_cb() {

  $fields = array();
  $errors = new WP_Error();

  // Buffer output
  ob_start();

  // Custom registration, go!
  reserv($fields, $errors);

  // Return buffer
//    return ob_get_clean();
}
if (isset($_GET['new'])) {
  reserv_cb();
}
?>