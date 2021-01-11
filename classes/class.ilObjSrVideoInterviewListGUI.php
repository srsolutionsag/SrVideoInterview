<?php

require_once __DIR__ . "/class.ilObjSrVideoInterviewGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewExerciseGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantGUI.php";

/**
 * Class ilObjSrVideoInterviewListGUI
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewListGUI extends ilObjectPluginListGUI
{
    public function __construct($a_context = self::CONTEXT_REPOSITORY)
    {
        global $DIC;
        parent::__construct($a_context);
        $DIC->ui()->mainTemplate()->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/css/default/UIComponent/style.general.css");
    }

    /**
     * @inheritDoc
     */
    public function initType() : void
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getGuiClass() : string
    {
        return ilObjSrVideoInterviewGUI::class;
    }

    /**
     * @ineritdoc
     * @return array
     */
    public function getProperties() : array
    {
        $parent = parent::getProperties();

        $parent[] = [
            'property' => $this->txt('status'),
            'value'    => "<div class=\"sr-status-light\" style=\"background-color: green;\"></div>"
        ];

        return $parent;
    }

    /**
     * initialises the base-commands of a VideoInterview.
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
