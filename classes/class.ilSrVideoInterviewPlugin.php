<?php

/**
 * Class ilSrVideoInterviewPlugin
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilSrVideoInterviewPlugin extends ilRepositoryObjectPlugin
{
    /**
     * @var string
     */
    const PLUGIN_ID   = 'xvin';

    /**
     * @var string
     */
    const PLUGIN_NAME = 'SrVideoInterview';

    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    protected function uninstallCustom()
    {
        // TODO: Implement uninstallCustom() method.
    }

}
