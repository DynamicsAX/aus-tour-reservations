<?php

/**
 * Created by PhpStorm.
 * User: eric
 * this form handles the form functionality
 */
class Form {
    private $output = '';
    public function __construct(Array $formOptions ,Array $fields, Array $errorsOrSuccess = array(), $title = '')
    {
        wp_enqueue_script( 'date-picker-js', plugin_dir_url(__FILE__).'scripts/jquery.datetimepicker.full.min.js',array('jquery'), '1.0.0', true );
        wp_enqueue_script( 'init-picker', plugin_dir_url(__FILE__).'scripts/init.js',array('jquery'), '1.0.0', true );
        wp_register_style( 'date-picker-css', plugin_dir_url(__FILE__).'scripts/jquery.datetimepicker.css' );
        wp_enqueue_style('date-picker-css');
        add_action( 'admin_enqueue_scripts',array($this, 'enqueueAdminScripts') );
        $defaults = array(
            'name' => 'form',
            'class' => '',
            'method' => 'get',
            'action' => ''
        );
        $params = array_replace_recursive($defaults, $formOptions);
        $output ='<div class="wrap">';
        if($title != ''){
            $output .= '<h2>'.$title.'</h2>';
        }
        if(isset($errorsOrSuccess['errors'])){
            $output .= $this->displayErrors($errorsOrSuccess['errors']);
        }
        if(isset($errorsOrSuccess['success'])){
            $output .= $this->displayNotification($errorsOrSuccess['success']);
        }
        $output .= '<form class="'.$params['class'].'" name="'.$params['name'].'" action="'.$params['action'].'" method="'.$params['method'].'">';


        foreach ($fields as $fieldName =>  $field){
            if($field['type'] == 'text'){
                $output .= $this->createTextField($fieldName,$field);
            }
            if($field['type'] == 'textarea'){
                $output .= $this->createTextArea($fieldName,$field);
            }
            if($field['type'] == 'select'){
                $output .= $this->createSelectField($fieldName,$field);
            }
            if($field['type'] == 'submit'){
                $output .= $this->createSubmitField($fieldName, $field);
            }
        }
        $output .= '</form>';
        $output .= '</div>';
        $this->output = $output;
    }

    public function enqueueAdminScripts()
    {

    }
    public function displayErrors(Array $errors)
    {
        $output = '<ul class="display" style="list-style-type: none; color: red;">';
        foreach ($errors as $error){
            $output .= "<li class='error'>".$error.'</li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function displayNotification(Array $errors)
    {
        $output = '<ul class="display" style="list-style-type: none; color: green;">';
        foreach ($errors as $error){
            $output .= "<li class='error'>".$error.'</li>';
        }
        $output .= '</ul>';
        return $output;
    }
    public function createSubmitField($name , Array $atts)
    {

        $defaults = array(
            "class" => "button-primary",
            "value" => "Save",
        );
        $desc = '';
        if(isset($params['description'])){
            $desc = '<span class="description">'. $params['description'] .'</span>';
        }
        $params = array_replace_recursive($defaults, $atts);
        $output = '<input class="'.$params['class'].'" type="submit" name="'.$name.'" value="'.( $params['value'] ).'" />';
        return $output.$desc;
    }
    public function __toString()
    {
        return $this->output;
    }
    public function createTextField($name , Array $atts)
    {
        $defaults = array(
            "class" => "",
            "value" => "",
            "id" => "",
            "readonly" => "",
        );

        $params = array_replace_recursive($defaults, $atts);
        $desc = '';
        if(isset($params['description'])){
            $desc = '<span class="description">'. $params['description'].'</span>';
        }
        return '<label for="'.$name.'">'.$atts['displayName'].'</label></br>
<input name="'.$name.'" type="text" value="'.$atts['value'].'"  id="'.$params['id'].'" class="regular-text '.$params['class'].'" autocomplete="off"  '.$params['readonly'].'/>'.$desc.'<br>';
    }
    public function createTextArea($name , Array $atts)
    {
        $defaults = array(
            "class" => "",
            "value" => "",
            "rows" => 10,
            "cols" => 30,
        );

        $params = array_replace_recursive($defaults, $atts);
        $desc = '';
        if(isset($params['description'])){
            $desc = '<span class="description">'. $params['description'].'</span>';
        }
        $output = '<label for="'.$name.'">'.$atts['displayName'].'</label></br>';
        $output .= '<textarea name="'.$name.'" cols="'.$params['cols'].'" rows="'.$params['rows'].'" class="regular-text '.$params['class'].'">'.$params['value'].'</textarea><br>';
        return $output;
    }

    public function createSelectField($name , Array $atts)
    {
        $defaults = array(
            "class" => "",
            "value" => "",
        );

        $params = array_replace_recursive($defaults, $atts);
        $desc = '';
        if(isset($params['description'])){
            $desc = '<span class="description">'. $params['description'] .'</span>';
        }
        $label = '<label for="'.$name.'">'.$atts['displayName'].'</label></br>';
        $output = '<select name="'.$name.'">';
        $output .= '<option value="" >Select</option>';
        foreach ($params['options'] as $option){
            if($option['id'] == $params['value'])
                $output .= '<option value="'. $option['id'].'" selected>'. $option['name'].'</option>';
            else
                $output .= '<option value="'. $option['id'].'" >'. $option['name'].'</option>';
        }
        $output .= '<select>';

        return $label.$output.$desc.'<br>';
    }

}