<?php namespace NpmWeb\FormBuilder;

class HtmlServiceProvider 
	extends \NpmWeb\ClientValidationGenerator\Laravel\HtmlServiceProvider
{

	protected function createFormBuilder($app) {
		// use this package's FormBuilder, not the default one
		return new FormBuilder( $app['html'], $app['url'], $app['session.store']->getToken());
	}

}
