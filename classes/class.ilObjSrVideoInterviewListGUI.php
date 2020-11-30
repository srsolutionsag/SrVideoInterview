<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php');
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewExerciseGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantGUI.php";

/**
 * Class ilObjSrVideoInterviewListGUI
 *
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
                'cmd'        => ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX,
                'default'    => true
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX,
                'default'    => false
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_EDIT,
                'txt'        => $this->txt('edit'),
                'default'    => false
            ),
        );
    }
}
