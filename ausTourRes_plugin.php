<?php
 /*
  Plugin Name: AustriaTour Reservations
  Description: Custom plugin used for registration management
  Version: 1
  Author: Victor Para
  Author URI: www.linkedin.com/in/victorpara
 */

// returns the root directory path of particular plugin
define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'ausTourReservations.php');

global $at_db_version;
$at_db_version = '1.0';
 
function at_datatable() {
    global $wpdb;
    global $at_db_version;

    $usersTable = $wpdb->prefix.'users';
    $table_employee_list = $wpdb->prefix.'employee_list';
    if($wpdb->get_var("SHOW TABLES LIKE '$table_employee_list'") == $table_employee_list) {

        add_option('at_db_version', $at_db_version);
        return;
    }
 
    $charset_collate = $wpdb->get_charset_collate();


    $sql = "CREATE TABLE $table_employee_list (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		user bigint(20) UNSIGNED NOT NULL,
		contact varchar(100) NOT NULL,
		UNIQUE KEY (id),		
		
		FOREIGN KEY (user)
			REFERENCES $usersTable(ID)
			ON UPDATE CASCADE ON DELETE RESTRICT
	);$charset_collate";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
	add_option('at_db_version', $at_db_version);
	$table_service = $wpdb->prefix.'service';
 
    $sql = "CREATE TABLE $table_service (
		id mediumint(9) NOT NULL AUTO_INCREMENT  PRIMARY KEY,
		name varchar(128) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);	
	add_option('at_db_version', $at_db_version);
	
	$table_car = $wpdb->prefix.'car';
 
    $sql = "CREATE TABLE $table_car (
		id mediumint(9) NOT NULL AUTO_INCREMENT  PRIMARY KEY,
		name varchar(128) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);	
	add_option('at_db_version', $at_db_version);	
	
	$table_ausTourReservations = $wpdb->prefix.'ausTourReservations';
	
	$sql = "CREATE TABLE $table_ausTourReservations (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		employeeId mediumint(9) NOT NULL,
		date DATE NOT NULL,
		serviceId mediumint(9) NOT NULL,		
		timeStart TIME NOT NULL,
		timeEnd TIME NOT NULL,
		hours DECIMAL (5) NOT NULL,
		route varchar(1024) NOT NULL,
		reservationTitle varchar(128) NOT NULL,
		PAX SMALLINT(5)  NOT NULL,
		carId mediumint(9) NOT NULL,
		comment TEXT,
		amount DECIMAL(10,2) NOT NULL,
		amountCash DECIMAL(10,2),		
		UNIQUE KEY id (id),
		
    	FOREIGN KEY (employeeId)
			REFERENCES $table_employee_list(id)
			ON UPDATE CASCADE ON DELETE RESTRICT,
		
    	FOREIGN KEY (serviceId)
			REFERENCES $table_service(id)
			ON UPDATE CASCADE ON DELETE RESTRICT,
    
		FOREIGN KEY (carId)
			REFERENCES $table_car(id)
			ON UPDATE CASCADE ON DELETE RESTRICT
	) $charset_collate;";
	
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    add_option('at_db_version', $at_db_version);
}
 
register_activation_hook(__FILE__, 'at_datatable');
?>