<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\Repository\ParticipantRepository;

/**
 * Class ilObjVideoInterviewParticipantGUI
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjVideoInterviewParticipantGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewParticipantGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * Participant GUI tab-names and translation var
     */
    const PARTICIPANT_TAB        = 'participant_tab';

    /**
     * Participant GUI commands
     */
    const CMD_PARTICIPANT_INDEX  = 'showAll';
    const CMD_PARTICIPANT_ADD    = 'addParticipant';
    const CMD_PARTICIPANT_REMOVE = 'removeParticipant';
    const CMD_PARTICIPANT_NOTIFY = 'notifyParticipants';

    /**
     * @var ParticipantRepository
     */
    protected $repository;

    /**
     * Initialise ilObjVideoInterviewParticipantGUI
     *
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        $this->repository = new ParticipantRepository();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * dispatches the given command and calls the corresponding method.
     */
    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::PARTICIPANT_TAB);
        $cmd = $this->ctrl->getCmd(self::CMD_PARTICIPANT_INDEX);

        switch ($cmd)
        {
            case self::CMD_PARTICIPANT_INDEX:
            case self::CMD_PARTICIPANT_ADD:
            case self::CMD_PARTICIPANT_REMOVE:
            case self::CMD_PARTICIPANT_NOTIFY:
                if ($this->access->checkAccess("write", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            default:
                // we should not reach this.
                break;
        }
    }

    protected function showAll() : void
    {

    }

    protected function addParticipant() : void
    {

    }

    protected function removeParticipant() : void
    {

    }

    protected function notifyParticipants() : void
    {

    }
}