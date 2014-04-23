<?php namespace NpmWeb\FormBuilder;

use NpmWeb\ClientValidationGenerator\Laravel\JqueryValidationGenerator;

class HtmlServiceProvider 
	extends \NpmWeb\ClientValidationGenerator\Laravel\HtmlServiceProvider
{

	/**
	 * Register the form builder instance.
	 *
	 * @return void
	 */
	protected function registerFormBuilder()
	{
		$this->app->bindShared('form', function($app)
		{
			$form = new FormBuilder( $app['html'], $app['url'], $app['session.store']->getToken());

			$form->setClientValidationGenerator( new JqueryValidationGenerator(true) );

			/* OLD WAY 
			$form->setClientValidationGenerator( \App::make(
				'NpmWeb\ClientValidationGenerator\ClientValidationGeneratorInterface') );
			*/
		
			return $form->setSessionStore($app['session.store']);
		});
	}

}
