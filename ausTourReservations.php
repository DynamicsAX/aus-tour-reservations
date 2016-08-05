<?php

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Reserv_List_Table extends WP_List_Table {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        parent::__construct( array(
            'singular'=> 'wp_list_text_link', //Singular label
            'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
            'ajax'   => false //We won't support Ajax for this table
        ) );
    }

    /**
     * Retrieve reservations data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_reservations( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT r.id, 
						r.reservationTitle, 
						r.date, 
						c.name car, 
						s.name service, 
						e.name employee, 
						r.unite, 
						r.timeStart, 
						r.timeEnd, 
						r.hours, 
						r.route, 
						r.PAX, 
						r.comment, 
						r.amount, 
						r.amountCash 
							FROM {$wpdb->prefix}ausTourReservations r, 
								{$wpdb->prefix}car c, 
								{$wpdb->prefix}employee_list e, 
								{$wpdb->prefix}service s								
								where r.carId = c.id
									AND r.employeeId = e.id
									AND r.serviceId = s.id";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Delete a reservation record.
     *
     * @param int $id reservation ID
     */
    public static function delete_reservation( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}ausTourReservations",
            array( 'ID' => $id ),
            array(  '%d' )
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ausTourReservations";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no reservations data is available */
    public function no_items() {
        _e( 'No reservations avaliable.', 'sp' );
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
            case 'date':
            case 'car':
            case 'service':
            case 'employee':
            case 'unite':
            case 'timeStart':
            case 'timeEnd':
            case 'hours':
            case 'route':
            case 'PAX':
            case 'comment':
            case 'amount':
            case 'amountCash':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_reservationTitle( $item ) {

        $delete_nonce = wp_create_nonce( 'sp_delete_reservation' );

        $title = '<strong>' . $item['reservationTitle'] . '</strong>';

        $actions = array(
            'delete' => sprintf( '<a href="?page=%s&action=%s&reservation=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }	

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'=> '<input type="checkbox" />',
            'id'=> __( 'ID', 'sp' ),
            'reservationTitle'=> __( 'Title', 'sp' ),
            'date'=> __( 'Date', 'sp' ),
            'employee'=> __( 'Employee', 'sp' ),
            'car'=> __( 'Car ID', 'sp' ),
            'service'=> __( 'Service', 'sp' ),
            'unite'=> __( 'Unite', 'sp' ),
            'timeStart'=> __( 'Start time', 'sp' ),
            'timeEnd'=> __( 'End time', 'sp' ),
            'hours'=> __( 'Hours', 'sp' ),
            'route'=> __( 'Route', 'sp' ),
            'PAX'=> __( 'PAX', 'sp' ),
            'comment'=> __( 'Comment', 'sp' ),
            'amount'=> __( 'Amount', 'sp' ),
            'amountCash'=> __( 'Amount cash', 'sp' )
        );

        return $columns;
    }
	
	public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'date' => array( 'date', true ),
            'timeStart' => array( 'timeStart', false ),
            'timeEnd' => array( 'timeEnd', false ),
            'employeeId' => array( 'employeeId', false ),
            'carId' => array( 'carId', false )
        );

        return $sortable_columns;
    }	

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete'
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'reservation_per_page', 50 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_reservations( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'sp_delete_reservation' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_reservation( absint( $_GET['reservation'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect( esc_url_raw(add_query_arg()) );
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_reservation( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect( esc_url_raw(add_query_arg()) );
            exit;
        }
    }
}

class SP_Plugin {

    // class instance
    static $instance;

    // reservation WP_List_Table object
    public $reservation_obj;

    // class constructor
    public function __construct() {
        add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
        add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
    }

    public static function set_screen( $status, $option, $value ) {
        return $value;
    }

    public function plugin_menu() {

        $hook = add_menu_page(
            'AustriaTour Reservation Management',
            'Austria Tour',
            'manage_austour',
            'aus-tour-reservations',
            array( $this, 'plugin_settings_page' )
        );
        add_submenu_page( 'aus-tour-reservations', 'employee_list', 'Add New Reservation', 'manage_austour', 'aus-tour-reservations&new=1', array($this, 'ausTourAddPage') );
        add_submenu_page('aus-tour-reservations','employee_list', 'Services', 'manage_options', 'aus-tour-service',  array($this, 'ausTourAddService') );
        add_submenu_page('aus-tour-reservations','employee_list', 'Employees', 'manage_options', 'aus-tour-employee', array($this, 'ausTourAddEmployee') );
        add_submenu_page('aus-tour-reservations','employee_list', 'Cars', 'manage_options', 'aus-tour-car',  array($this, 'ausTourAddCar') );
        add_action( "load-$hook", array( $this, 'screen_option' ) );
    }


    function ausTourAddPage(){
//        include_once (plugin_dir_path(__FILE__).'forms/reservation-new.php');
        require_once (plugin_dir_path(__FILE__).'oop/reservation.php');
    }
    function ausTourAddEmployee(){
        include_once (plugin_dir_path(__FILE__).'oop/employee.php');
    }
    function ausTourAddService(){
//        include_once (plugin_dir_path(__FILE__).'forms/service-new.php');
        include_once (plugin_dir_path(__FILE__).'oop/service.php');
    }
    function ausTourAddCar(){
        include_once (plugin_dir_path(__FILE__).'oop/car.php');
    }
    /**
     * Plugin settings page
     */
    public function plugin_settings_page() {
        require_once(plugin_dir_path(__FILE__).'oop/reservation.php');

    }

    /**
     * Screen options
     */
    public function screen_option() {

        $option = 'per_page';
        $args   = array(
            'label'   => 'Reservations',
            'default' => 50,
            'option'  => 'Reservations_per_page'
        );

        add_screen_option( $option, $args );

        $this->reservation_obj = new Reserv_List_Table();
    }


    /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}
function getIns () {
    SP_Plugin::get_instance();
}

add_action( 'plugins_loaded', 'getIns' );



function reservation_list() {
    //Prepare Table of elements
    $wp_list_table = new Reserv_List_Table();
    $wp_list_table->prepare_items();
    
    //Table of elements
    $wp_list_table->display();
}
?>
<?php
function employee_list() {
    ?>
    <style>
        table {
            border-collapse: collapse;
        }

        table, td, th {
            border: 1px solid black;
            padding: 20px;
            text-align: center;
        }
    </style>
    <div class="wrap">
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>User</th>
                <th>Role</th>
                <th>Contact</th>
            </tr>
            </thead>
            <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix.'employee_list';
            $employees = $wpdb->get_results("SELECT id,name,user,contact,role from $table_name");
            foreach ($employees as $employee) {
                ?>
                <tr>
                    <td><?= $employee->id; ?></td>
                    <td><?= $employee->name; ?></td>
                    <td><?= $employee->user; ?></td>
                    <td><?= $employee->role; ?></td>
                    <td><?= $employee->contact; ?></td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
    <?php
}

add_shortcode('full_employee_list', 'employee_list');
?>