<?php

/**
 * Class ilObjSrVideoInterviewGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewExerciseGUI, ilObjSrVideoInterviewAnswerGUI, ilObjSrVideoInterviewParticipantGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    /**
     * Repository Object settings tab
     */
    const VIDEO_INTERVIEW_TAB_INDEX = 'settings';

    /**
     * Repository Object commands
     */
    const CMD_VIDEO_INTERVIEW_EDIT  = 'update';

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;

    /**
     * @var ILIAS\Refinery\Factory
     */
    protected $refinery;

    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;

    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;

    /**
     * Initialise ilObjSrVideoInterviewGUI
     *
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->refinery    = $DIC->refinery();
        $this->http        = $DIC->http();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * @inheritDoc
     * @return string
     */
    public final function getType() : string
    {
        return ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public final function getAfterCreationCmd() : string
    {
        return ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
    }

    /**
     * @return string
     */
    public final function getStandardCmd() : string
    {
        return ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
    }

    /**
     * dispatches the next-class and delegates it to the corresponding GUI class.
     *
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class)
        {
            case '':
            case strtolower(ilObjSrVideoInterviewExerciseGUI::class):
                if (!$this->getCreationMode()) {
                    $exercise_gui = new ilObjSrVideoInterviewExerciseGUI($this->ref_id);
                    $this->ctrl->forwardCommand($exercise_gui);
                }
                break;
            case strtolower(ilObjSrVideoInterviewAnswerGUI::class):
                $answer_gui = new ilObjSrVideoInterviewAnswerGUI($this->ref_id);
                $this->ctrl->forwardCommand($answer_gui);
                break;
            case strtolower(ilObjSrVideoInterviewParticipantGUI::class):
                $participant_gui = new ilObjSrVideoInterviewParticipantGUI($this->ref_id);
                $this->ctrl->forwardCommand($participant_gui);
                break;
            default:
                $this->permissionDenied();
                break;
        }

        parent::executeCommand();
    }

    /**
     * dispatches the given command and calls the corresponding method.
     *
     * @param string $cmd
     */
    public function performCommand(string $cmd) : void
    {
        switch ($cmd)
        {
            case self::CMD_VIDEO_INTERVIEW_EDIT:
                if ($this->access->checkAccess("write", $cmd, $this->ref_id)) {
                    $this->edit();
                }
                break;
            default:
                $this->permissionDenied();
                break;
        }
    }

    /**
     * creates object tabs and links the corresponding plugin GUI classes.
     */
    protected function setTabs() : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id))
        {
            $this->tabs->addTab(
                // Exercise tab
                ilObjSrVideoInterviewExerciseGUI::EXERCISE_TAB_INDEX,
                $this->txt(ilObjSrVideoInterviewExerciseGUI::EXERCISE_TAB_INDEX),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewExerciseGUI::class,
                    ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX
                )
            );
        }

        if ($this->access->checkAccess("write", "", $this->ref_id))
        {
            // edit() ??
            //
            $this->tabs->addTab(
                // Settings tab
                self::VIDEO_INTERVIEW_TAB_INDEX,
                $this->txt(self::VIDEO_INTERVIEW_TAB_INDEX),
                $this->ctrl->getLinkTargetByClass(
                    self::class,
                    self::CMD_VIDEO_INTERVIEW_EDIT,
                )
            );

            $this->tabs->addTab(
                // Participant tab
                ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB_INDEX,
                $this->txt(ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB_INDEX),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewParticipantGUI::class,
                    ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX,
                )
            );
        }

        parent::setTabs();
    }

    /**
     * renders a "permission denied" error toast.
     */
    protected function permissionDenied() : void
    {
        // @TODO: implement permissionDenied() method.
    }
}
