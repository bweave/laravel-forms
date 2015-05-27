<?php namespace NpmWeb\FormBuilder;

use NpmWeb\FormBuilder\Renderers\RendererManager;

class HtmlServiceProvider
    extends \NpmWeb\ClientValidationGenerator\Laravel\HtmlServiceProvider
{

    protected $configFilePath;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->configFilePath = __DIR__.'/../../config/config.php';
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // @see https://coderwall.com/p/svocrg
        // This one actually works without these params, but putting it
        // here in case duplicated in the future. If you put the
        // ServiceProvider in a subdir, you have to specify a
        // non-default path
        $this->publishes([ $this->configFilePath => config_path('forms.php')]);
    }

    public function register()
    {
        $this->mergeConfigFrom( $this->configFilePath, 'forms' );
        return parent::register();
    }

    protected function createFormBuilder($app) {
        // use this package's FormBuilder, not the default one
        return new FormBuilder(
            $app['html'],
            $app['url'],
            $app['session.store']->getToken(),
            $this->createRenderer($app)
        );
    }

    protected function createRenderer($app) {
        return (new RendererManager($app))->driver();
    }

}
