<?php

use srag\CustomInputGUIs\UserTakeOver\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";
require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantTableGUI.php";

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
    const CMD_PARTICIPANT_SEARCH = 'searchParticipant';

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * Initialise ilObjVideoInterviewParticipantGUI
     *
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();

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
        $b = new \srag\CustomInputGUIs\TextInputGUI\TextInputGUIWithModernAutoComplete("test", "test");
        $b->setDisableHtmlAutoComplete(false);
        $b->setDataSource(
            $this->ctrl->getLinkTargetByClass(
                self::class,
                self::CMD_PARTICIPANT_SEARCH,
                "",
                true
            )
        );

        $a = new ilTextInputGUI("title", "postvar");
        $a->setDisableHtmlAutoComplete(true);
        $a->setDataSource(
            $this->ctrl->getLinkTargetByClass(
                self::class,
                self::CMD_PARTICIPANT_SEARCH,
                "",
                true
            )
        );

        $data = json_encode(array(
            1 => 'test1',
            2 => 'test2',
            3 => 'test3'
        ));

//        echo var_dump($data); exit;

        $response = $this->http->response()->withBody(Stream);
        echo var_dump($response); exit;

        $this->toolbar->addInputItem($b);

        $participants = $this->repository->getParticipantsByExerciseId(6);

        $table_gui = new ilObjSrVideoInterviewParticipantTableGUI($this, self::CMD_PARTICIPANT_INDEX);
        $table_gui->setData($participants);

        $this->tpl->setContent($table_gui->getHTML());
    }

    /**
     * addParticipant()s key-autocomplete ajax data source
     *
     * @throws \ILIAS\HTTP\Response\Sender\ResponseSendingException
     */
    protected function searchParticipant() : void
    {
        $postvar = $this->ctrl->getParameterArrayByClass(
            self::class,
            self::CMD_PARTICIPANT_SEARCH,
        )[0];

        $data = json_encode(array(
            1 => 'test1',
            2 => 'test2',
            3 => 'test3'
        ));

        $response = $this->http->response()->withBody(ILIAS\Filesystem\Stream\Streams::ofString($data));


        $response->withHeader('Content-Type', 'application/json');
        $this->http->saveResponse($response);
        $this->http->sendResponse();
        $this->http->close();
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