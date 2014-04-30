<?php namespace NpmWeb\FormBuilder;

class HtmlServiceProvider 
	extends \NpmWeb\ClientValidationGenerator\Laravel\FoundationHtmlServiceProvider
{

	protected function createFormBuilder($app) {
		// use this package's FormBuilder, not the default one
		return new FormBuilder( $app['html'], $app['url'], $app['session.store']->getToken());
	}

}
