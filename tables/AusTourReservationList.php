<script type="text/javascript">
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>

<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 6/27/16
 * Time: 4:35 PM
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class AusTourReservationList extends WP_List_Table
{
    public static $total;
    public static $totalCash;
    private static $_TABLE = 'ausTourReservations';
    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Reservation', 'sp' ), //singular name of the listed records
            'plural'   => __( 'Reservations', 'sp' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?

        ] );

    }
    function extra_tablenav( $which ) {
        global $wpdb, $testiURL, $tablename, $tablet;
        $move_on_url = '&date-filter=';

        if ( $which == "top" ){
            $table_employee_list = $wpdb->prefix.'employee_list';
            $options = $wpdb->get_results( "SELECT id,name FROM $table_employee_list", ARRAY_A );


            ?>
            <div class="alignleft actions bulkactions ewc-filter-cat">
            <div class="filters">
            </div>
                <label for="date-filter"><?php echo L::from?>: </label>
                <input type="text" name="from-filter" class="date-from datePickerMain" value="<?php echo $_REQUEST['from-filter'];?>">
                <label for="date-filter"><?php echo L::to?>: </label>
                <input type="text" name="to-filter" class="date-to datePickerMain" value="<?php echo $_REQUEST['to-filter'];?>">
                <?php

                ?>
                <label for="employee-filter"> | <?php echo L::employee?>: </label>
                <style>
                    .select{
                        float: none !important;
                    }
                </style>
                <select name="employee-filter" class="select">
                    <option value="" ><?php echo L::select?></option>
                <?php

                foreach ($options as $option) {
                    if ($option['id'] == $_REQUEST['employee-filter']) {
                        ?>
                        <option value="<?= $option['id'] ?>" selected><?php echo $option['name'] ?></option>
                        <?php
                    }
                    else {
                        ?>
                        <option value="<?= $option['id'] ?>"><?php echo  $option['name'] ?></option>
                        <?php
                    }
                }
                ?>
                    </select>


                <?php
                submit_button( L::filter, 'button', 'filter_action', false, array( 'id' => 'filter-query-submit' ) );
                ?>				
				
            </div>

<?php

        }
        if ( $which == "bottom" ){
		?>
            <input type="button" onclick="printDiv('poststuff')" value="Print" />			
<?php
        }
        return $move_on_url;
    }
    public static function getReservations($perPage = 5, $pageNumber= 1)
    {

        global $wpdb;

        $sql = "SELECT r.id, 
						r.reservationTitle, 
						r.date, 
						c.name car, 
						s.name service, 
						e.name employee, 
						r.hours,
						TIME_FORMAT(r.timeStart, '%H:%i') AS timeStart,
						TIME_FORMAT(r.timeEnd, '%H:%i') AS timeEnd,
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
        if(!empty( $_REQUEST['from-filter'] )){
            $sql .=  ' and r.date>= "' . $_REQUEST['from-filter'].'"';
        }
        if(!empty( $_REQUEST['to-filter'] )){
            $sql .=  ' and r.date<= "' . $_REQUEST['to-filter'].'"';
        }
        if(!empty( $_REQUEST['employee-filter'] )){
            $sql .=  ' and e.id= "' . $_REQUEST['employee-filter'].'"';
        }
        $sql .= " LIMIT $perPage";

        $sql .= ' OFFSET ' . ( $pageNumber - 1 ) * $perPage;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        $total = 0;
        $totalCash = 0;
        foreach ($result as $r){
            $total += $r['amount'];
            $totalCash += $r['amountCash'];
        }
        self::$total = $total;
        self::$totalCash = $totalCash;
        return $result;
    }

    public static function deleteReservation($id)
    {
        global $wpdb;
        $del = $wpdb->delete(
            "{$wpdb->prefix}".self::$_TABLE,
            [ 'id' => $id ],
            [ '%d' ]
        );

    }
	
	public static function sendSMS($id)
	{
		global $wpdb;
		$table_employee_list = $wpdb->prefix.'employee_list';
		$table_reservations = $wpdb->prefix.'ausTourReservations';
		$curRec = $wpdb->get_results($wpdb->prepare("SELECT r.reservationTitle, 
						r.date, 
						c.name car, 
						s.name service, 
						e.name employee,
						e.contact empPhone,						
						r.unite,
						TIME_FORMAT(r.timeStart, '%%H:%%i') AS timeStart,
						TIME_FORMAT(r.timeEnd, '%%H:%%i') AS timeEnd,
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
									AND r.serviceId = s.id
									AND r.id =%d", $id ),ARRAY_A);
																
		$id="72830002";  //Get this under the Web API section
		$password="cirlan333";  //You need to set this under Account Profile
		$type="A";		//A for ASCII message content
		$mobile= $curRec[0]['empPhone'];  //Change this to a valid mobile number, alwasy include country code
		$message=$curRec[0]['date']." | ".date("g:i",$curRec[0]['timeStart'])." | ".$curRec[0]['route']." | ".$curRec[0]['reservationTitle']." | Px".$curRec[0]['PAX']." | ".$curRec[0]['car'];
		
		
		echo $message;
		$message = urlencode($message);
		
		
		echo $mobile;

		$send_url = "https://www.commzgate.net/gateway/SendMsg?ID=" . $id . "&Password=" . $password . "&Mobile=" . $mobile . "&Type=" . $type . "&Message=" . $message . "\n";

		$response = wp_remote_post($send_url);
		
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			   echo 'Reservation '.$curRec[0]['reservationTitle'].' successfully sent.';
		}
	}	

    /** Text displayed when no reservation data is available */
    public function no_items() {
        _e( 'No Reservations available.', 'sp' );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_reservationTitle( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sp_delete_reservation' );

        $title = '<strong>' . $item['reservationTitle'] . '</strong>';

        $actions = [
			'edit'   => sprintf('<a href="?page=%s&action=%s&reservation=%s">'.L::edit.'</a>',  $_REQUEST['page'],'edit', absint( $item['id'] )),
            'delete' => sprintf( '<a href="?page=%s&action=%s&reservation=%s&_wpnonce=%s">'.L::delete.'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
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
            case 'reservationTitle':
            case 'car':
            case 'service':
            case 'employee':
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
                return print_r( $column_name, true ); //Show the whole array for troubleshooting purposes
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
            '<input type="checkbox" name="bulk[]" value="%s" />', $item['id']
        );
    }
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'=> '<input type="checkbox" />',            
            'reservationTitle'=> __( 'Title', 'sp' ),
            'date'=> __( 'Date', 'sp' ),
            'employee'=> __( 'Employee', 'sp' ),
            'car'=> __( 'Car ID', 'sp' ),
            'service'=> __( 'Service', 'sp' ),
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
            'employee' => array( 'employee', true ),
			'date'=> array( 'Date', 'sp' ),
			'reservationTitle'=> array( 'reservationTitle', 'sp' ),
            'car' => array( 'car', false ),
			'timeStart'=> array( 'timeStart', 'sp' ),
            'timeEnd' => array( 'timeEnd', false ),
            'service' => array( 'service', false ),
            'PAX' => array( 'PAX', false ),
            'amount' => array( 'amount', false ),
			'amountCash'=> array( 'amountCash', 'sp' )
        );

        return $sortable_columns;
    }
    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => 'Delete',
			'bulk-sendSMS' => 'Send SMS'
        ];

        return $actions;
    }
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'reservations_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = self::getReservations( $per_page, $current_page );

    }
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}".self::$_TABLE;

        return $wpdb->get_var( $sql );
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

                self::deleteReservation( absint( $_GET['reservation'] ) );

                /*wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce','action','reservation' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
//                wp_redirect( esc_url( add_query_arg() ) );
                exit;*/
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::deleteReservation( $id );

            }
            /*
                        wp_redirect( esc_url( add_query_arg() ) );
                        exit;*/
        }
		
		// If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-sendSMS' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-sendSMS' )
        ) {

            $sendSMS_ids = esc_sql( $_POST['bulk'] );

            // loop over the array of record IDs and delete them
            foreach ( $sendSMS_ids as $id ) {
                self::sendSMS( $id );
            }
        }		
    }
    /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}