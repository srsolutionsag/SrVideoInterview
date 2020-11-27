<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php');
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewManagementGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewSettingsGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewContentGUI.php";

/**
 * Class ilObjSrVideoInterviewListGUI
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewListGUI extends ilObjectPluginListGUI
{
    public function getGuiClass()
    {
        return ilObjSrVideoInterviewGUI::class;
    }

    public function initType()
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * @return array
     */
    public function initCommands()
    {
        $this->copy_enabled = false;
        $this->enableTags(true);

        return array(
            array(
                'permission' => 'read',
                'cmd'        => ilObjSrVideoInterviewContentGUI::CMD_INDEX,
                'default'    => true
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewManagementGUI::CMD_MANAGE,
                'default'    => false
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewSettingsGUI::CMD_SETTINGS_SHOW,
                'txt'        => $this->txt('edit'),
                'default'    => false
            ),
        );
    }
}
