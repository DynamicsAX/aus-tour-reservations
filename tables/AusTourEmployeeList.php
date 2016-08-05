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
class AusTourEmployeeList extends WP_List_Table
{
    private static $_TABLE = 'employee_list';	
    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Employee', 'sp' ), //singular name of the listed records
            'plural'   => __( 'Employees', 'sp' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?

        ] );

    }

    public static function getEmployees($perPage = 5, $pageNumber= 1)
    {

        global $wpdb;

        $sql = "SELECT e.id,
						e.name,
						e.contact,
						e.role,
						u.display_Name						
						FROM 
							{$wpdb->prefix}employee_list e,
							{$wpdb->prefix}users u
							where e.user = u.ID";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $perPage";

        $sql .= ' OFFSET ' . ( $pageNumber - 1 ) * $perPage;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public static function deleteEmployee($id)
    {
        global $wpdb;
        $del = $wpdb->delete(
            "{$wpdb->prefix}".self::$_TABLE,
            [ 'id' => $id ],
            [ '%d' ]
        );

    }

    /** Text displayed when no employee data is available */
    public function no_items() {
        _e( 'No '.self::$_TABLE.' available.', 'sp' );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sp_delete_employee' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'edit'   => sprintf('<a href="?page=%s&action=%s&employee=%s">'.L::edit.'</a>',  $_REQUEST['page'],'edit', absint( $item['id'] )),
            'delete' => sprintf( '<a href="?page=%s&action=%s&employee=%s&_wpnonce=%s">'.L::delete.'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
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
            case 'name':
            case 'display_Name':
            case 'contact':
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
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'   => '<input type="checkbox" />',
            'id'   => __('ID'),
            'name' => L::nameUser,
            'display_Name' => L::user,
            'contact' => L::phone,
        );
        return $columns;
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
            'id' => array( 'ID', true ),
            'name' => array( 'Name', true ),
            'user' => array( 'User', true ),
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
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'employees_per_page', 5 );
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
        $this->process_bulk_action();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = self::getEmployees( $per_page, $current_page );

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

            if ( ! wp_verify_nonce( $nonce, 'sp_delete_employee' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {

                self::deleteEmployee( absint( $_GET['employee'] ) );

                /*wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce','action','employee' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
//                wp_redirect( esc_url( add_query_arg() ) );
                exit;*/
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::deleteEmployee( $id );

            }
/*
            wp_redirect( esc_url( add_query_arg() ) );
            exit;*/
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