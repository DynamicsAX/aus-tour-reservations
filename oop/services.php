<?php

/**
 * Created by PhpStorm.
 * User: eric
 * Date: 7/22/16
 * Time: 11:10 AM
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
abstract class services
{
    protected $errors = array();
    protected $success = array();
    protected $tableName;
    protected $serviceName;
    protected $fieldCheck;
    protected $i18n;
    public function __construct()
    {
        require_once(plugin_dir_path(__FILE__) . '../forms/Form.php');
        require_once(plugin_dir_path(__FILE__) . '../forms/includes/gump.class.php');
        $this->init();

        $this->init();
        $this->lang();
        if(isset($_POST[$this->fieldCheck])){
            $processed = $this->process();
            if(is_array($processed)){
                $this->errors = $processed;
            }
        }



        $errorsOrSuccess = $this->errorsOrSuccess();

        $fields = $this->fieldsAndValues();
        $formOptions = array(
            'name' => 'form',
            'class' => '',
            'method' => 'post',
        );
        if (isset($_GET['new'])) {
            $form = new Form($formOptions, $fields, $errorsOrSuccess, L::newRecord);
            echo $form;
            return;
        }
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'edit' :
                    $form = new Form($formOptions, $fields, $errorsOrSuccess, L::editRecord);
                    echo $form;
                    return;
                    break;

            }
        }

        $this->showTable();
    }

    public function lang($lang = 'ru')
    {
        require_once (plugin_dir_path(__FILE__) .'language/php-i18n/i18n.class.php');
        $i18n = new i18n();
        $i18n->setCachePath(plugin_dir_path(__FILE__) .'language/php-i18n/tmp/cache');
        $i18n->setFilePath(plugin_dir_path(__FILE__) .'language/php-i18n/lang/lang_{LANGUAGE}.ini'); // language file path
        $i18n->setFallbackLang($lang);
        $i18n->setForcedLang("ru"); // force english, even if another user language is available
        $i18n->setSectionSeperator('_');
        $i18n->init();
    }
    public function errorsOrSuccess()
    {

        $errorsOrSuccess = array();
        $success = $this->success ;
        $errors = $this->errors ;
        if ($errors) {
            $errorsOrSuccess['errors'] = $errors;
        }
        if ($success) {
            $errorsOrSuccess['success'] = $success;
        }
        return $errorsOrSuccess;
    }
    public abstract function init();
    public function loadScript()
    {
        wp_enqueue_script('date-picker-js', plugin_dir_url(__FILE__) . 'scripts/jquery.datetimepicker.full.min.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('init-picker', plugin_dir_url(__FILE__) . 'scripts/init.js', array('jquery'), '1.0.0', true);

    }

    public function actions()
    {
        add_action('wp_head',$this,'loadStyle');
        add_action('wp_footer',$this,'loadScript');
    }
    public function loadStyle()
    {
        wp_register_style('date-picker-css', plugin_dir_url(__FILE__) . 'scripts/jquery.datetimepicker.css');
        wp_enqueue_style('date-picker-css');
    }
    public function process(){

        $is_valid = $this->validate();
        if (!is_array($is_valid) && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
            $this->update($_REQUEST[$this->serviceName]);
        }
        elseif (!is_array($is_valid)){
            $this->insert();
        }
        return $is_valid;
    }
    public abstract function showTable();
    public abstract function fieldsAndValues();
    public abstract function validate();
    public abstract function insert();
    public abstract function update($id);
    public abstract function createFields($values);
    public function showForm($formOptions,$fields, $errorsOrSuccess,$formTitle)
    {
        $this->screenOptions();
        $form = new Form($formOptions, $fields, $errorsOrSuccess, $formTitle);
        echo $form;
    }
}