<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/vendor/autoload.php');

/**
 * Class ilSrVideoInterviewPlugin
 * @author Fabian Schmid <fs@studer-raimann.ch>
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

    final public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    final protected function uninstallCustom() : void
    {
        // TODO: Implement uninstallCustom() method, remove database tables
    }
}
