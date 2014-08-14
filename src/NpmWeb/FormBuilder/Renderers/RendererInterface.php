<?php namespace NpmWeb\FormBuilder\Renderers;

interface RendererInterface {

    public function processOptions( $config, $type );

    public function renderFormControl( $fieldname, $config, $error, $control );

    public function renderCheckboxOrRadio( $fieldname, $config, $error, $control );

}
