<?php

require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/vendor/autoload.php';

use srag\Plugins\SrVideoInterview\UIComponent\Loader;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\DI\Container;
use ILIAS\UI\Renderer;

/**
 * ilSrVideoInterviewPlugin is the singleton plugin instance, which is primarily used
 * to provide a global txt() method, that is used in GUI-classes.
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilSrVideoInterviewPlugin extends ilRepositoryObjectPlugin
{
    /**
     * @var string
     */
    const PLUGIN_ID = 'xvin';

    /**
     * @var string
     */
    const PLUGIN_NAME = 'SrVideoInterview';

    /**
     * @var ilSrVideoInterviewPlugin
     */
    protected static $instance;

    /**
     * @return static
     */
    final public static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     * @return string
     */
    final public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * run the plugin deinstall-stuff here, currently removes the AREntity tables.
     */
    final protected function uninstallCustom() : void
    {
        // TODO: Implement uninstallCustom() method, remove database tables
    }

    /**
     * checks if given Component is a custom one and exchanges the default Renderer if so.
     *
     * @param Container $dic
     * @return Closure
     */
    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic) : Closure
    {
        $loader = new Loader($dic, $this);
        return static function ($dic) use ($loader) {
            return new class($loader) extends DefaultRenderer implements Renderer {

            };
        };
    }
}
