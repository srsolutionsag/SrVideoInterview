<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php');

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
                'cmd'        => ilObjSrVideoInterviewGUI::CMD_INDEX,
                'default'    => true
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewGUI::CMD_MANAGE,
                'default'    => false
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewGUI::CMD_EDIT,
                'txt'        => $this->txt('edit'),
                'default'    => false
            ),
        );
    }

    public function initType()
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

}
