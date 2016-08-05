<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/22/16
 * Time: 1:42 PM
 */
require_once(plugin_dir_path(__FILE__) .'services.php');

class car extends services
{

    public function init()
    {

        global $wpdb;
        $this->tableName = $wpdb->prefix . 'car';
        $this->serviceName = 'car';
        $this->fieldCheck = 'name';
    }

    public function showTable()
    {
        include_once(plugin_dir_path(__FILE__) . '../tables/AusTourCarList.php');


        $option = 'per_page';
        $args = [
            'label' => L::car,
            'default' => 5,
            'option' => 'cars_per_page'
        ];
        $ausTourCarList = new AusTourCarList();
        add_screen_option($option, $args);
        ?>
        <div class="wrap">
            <h2><?php echo L::cars?></h2>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <a href="<?php echo $url = admin_url('admin.php?page=aus-tour-car&new=true'); ?>"
                                   class="page-title-action"><?php echo L::addnew?></a>
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

        <?php
    }

    public function fieldsAndValues()
    {
        global $wpdb;
        $errors = $this->errors;
        $values = array(
            'name' =>(($errors) ? $_POST['name'] : '' ),
        );

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
            $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tableName} WHERE `id`= %s ", $_REQUEST['car']), OBJECT);
            //form fields
            $values = array(
                'name' => (($errors) ? $_POST['name'] : $edit->name),
            );
        }
        return $this->createFields($values);
    }

    public function validate()
    {
        $is_valid = GUMP::is_valid($_POST, array(
            'name' => 'required|max_len,250'
        ));
        return $is_valid;
    }

    public function insert()
    {
        global $wpdb;
        $wpdb->insert(
            $this->tableName,
            array('name' => $_POST['name'])
        );
        $this->success = array(L::added);
    }

    public function update($id)
    {
        global $wpdb;
        $wpdb->update(
            $this->tableName,
            array(
                'name' => $_POST['name']
            ),
            array('id' => $id)
        );

        $this->success = array(L::updated);
    }

    public function createFields($values)
    {
        //form fields
        $fields = array(
            'name' => array(
                'type' => 'text',
                'value' => $values['name'],
                'displayName' => ucfirst(L::idnumber),
            ),
            'save' => array(
                'type' => 'submit',
				'value' => ucfirst(L::save)				
            )
        );
        return $fields;
    }
}
new car();