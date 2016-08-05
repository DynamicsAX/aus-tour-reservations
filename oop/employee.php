<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/22/16
 * Time: 2:09 PM
 */
require_once('services.php');

class employee extends services
{

    public function init()
    {
        global $wpdb;
        $this->tableName = $wpdb->prefix . 'employee_list';
        $this->serviceName = 'employee';
        $this->fieldCheck = 'name';
    }

    public function showTable()
    {
        include_once(plugin_dir_path(__FILE__) . '../tables/AusTourEmployeeList.php');


        $option = 'per_page';
        $args = [
            'label' => 'Cars',
            'default' => 5,
            'option' => 'cars_per_page'
        ];
        $ausTourEmployeeList = new AusTourEmployeeList();
        add_screen_option($option, $args);
        ?>
        <div class="wrap">
            <h2><?php echo L::employees?></h2>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-employee&new=true'); ?>"
                                   class="page-title-action"><?php echo L::addnew?></a>
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
        <?php
    }

    public function fieldsAndValues()
    {
        global $wpdb;
        $errors = $this->errors;
        $values = array(
            'name' =>(($errors) ? $_POST['name'] : '' ),
            'user' =>(($errors) ? $_POST['user'] : '' ),
            'role' =>(($errors) ? $_POST['role'] : '' ),
            'contact' =>(($errors) ? $_POST['contact'] : '' ),
        );

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
            $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE `id`= %s ", $_REQUEST['employee']), OBJECT);
            //form fields
            $values = array(
                'name' => (($errors) ? $_POST['name'] : $edit->name),
                'user' => (($errors) ? $_POST['user'] : $edit->user),
                'role' => (($errors) ? $_POST['role'] : $edit->role),
                'contact' => (($errors) ? $_POST['contact'] : $edit->contact),
            );
        }
        return $this->createFields($values);
    }

    public function validate()
    {
        $is_valid = GUMP::is_valid($_POST, array(
            'name' => 'required|max_len,100',
            'user' => 'required|integer',
            'role' => 'required|max_len,100',
            'contact' => 'required|max_len,13',
        ));
        return $is_valid;
    }

    public function insert()
    {
        global $wpdb;
        $wpdb->insert(
            $this->tableName,
            array(
                'name' => $_POST['name'],
                'user' => $_POST['user'],
                'role' => $_POST['role'],
                'contact' => $_POST['contact'],
            )
        );
        $this->success = array(L::added);
    }

    public function update($id)
    {
        global $wpdb;
        $wpdb->update(
            $this->tableName,
            array(
                'name' => $_POST['name'],
                'user' => $_POST['user'],
                'role' => $_POST['role'],
                'contact' => $_POST['contact'],
            ),
            array('id'=>$id)
        );
        $this->success = array(L::updated);
    }

    public function createFields($values)
    {

        global $wpdb;
        $tableUsers = $wpdb->prefix.'users';
        //form fields
        $fields = array(
            'name' => array(
                'type' => 'text',
                'value' => $values['name'],
                'displayName' => ucfirst(L::nameUser),
            ),
            'user' => array(
                'type' => 'select',
                'value' =>$values['user'],
                'displayName' => ucfirst(L::user),
                'options' => $wpdb->get_results( "SELECT ID as id,display_name as name FROM $tableUsers", ARRAY_A ),
            ),
            'role' => array(
                'type' => 'text',
                'value' => $values['role'],
                'displayName' => ucfirst(L::role),
            ),
            'contact' => array(
                'type' => 'text',
                'value' => $values['contact'],
                'displayName' => ucfirst(L::phone),
            ),
            'save' => array(
                'type' => 'submit',
				'value' => ucfirst(L::save)
            )
        );
        return $fields;
    }
}

new employee();