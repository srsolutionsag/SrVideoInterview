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
     * centralized translation handling and better access.
     *
     * @var array
     */
    public static array $translations;

    public function __construct()
    {
        parent::__construct();

        self::$translations = [
            # content page

            # settings page
            'detailed_description'  => $this->txt('xvin_detailed_description'),
            'back_to'               => $this->txt('xvin_back_to'),
            'exercise_not_found'    => $this->txt('xvin_exercise_not_found'),

            # management page

            # general actions
            'title'         => $this->txt('title'),
            'description'   => $this->txt('description'),
            'save'          => $this->txt('save'),
            'update'        => $this->txt('update'),
            'properties'    => $this->txt('properties'),
            'content'       => $this->txt('content'),
            'copy'          => $this->txt('copy'),
            'edit'          => $this->txt('edit'),
            'export'        => $this->txt('export'),
        ];
    }

    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    protected function uninstallCustom() : void
    {
        // TODO: Implement uninstallCustom() method, remove database tables
    }
}
