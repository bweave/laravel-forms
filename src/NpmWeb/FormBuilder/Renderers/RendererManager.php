<?php namespace NpmWeb\FormBuilder\Renderers;

use Illuminate\Support\Manager;

class RendererManager extends Manager {

    static $packageName = 'forms';

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    protected function createDriver($driver)
    {
        $renderer = parent::createDriver($driver);

        // any other setup needed

        return $renderer;
    }

    /**
     * Create an instance of the Bootstrap basic grid driver.
     *
     * @return \NpmWeb\FormBuilder\Renderers\BootstrapBasicGridRenderer
     */
    public function createBootstrapBasicGridDriver()
    {
        return new BootstrapBasicGridRenderer();
    }

    /**
     * Create an instance of the Foundation basic grid driver.
     *
     * @return \NpmWeb\FormBuilder\Renderers\FoundationBasicGridRenderer
     */
    public function createFoundationBasicGridDriver()
    {
        return new FoundationBasicGridRenderer();
    }

    /**
     * Get the default renderer driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $driver = $this->app['config']->get(self::$packageName.'.driver');
        return $driver;
    }

    /**
     * Set the default renderer driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']->set(self::$packageName.'.driver', $name);
    }

}
