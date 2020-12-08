<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";
require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantTableGUI.php";

use srag\CustomInputGUIs\TextInputGUI\TextInputGUIWithModernAutoComplete;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;

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
            case self::CMD_PARTICIPANT_SEARCH:
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
        $this->toolbar->setPreventDoubleSubmission(true);
        $this->toolbar->setFormAction(
            $this->ctrl->getFormActionByClass(
                self::class,
                self::CMD_PARTICIPANT_ADD
            )
        );

        $user_field = new TextInputGUIWithModernAutoComplete("", "user_data");
        //$user_field->setMulti(true);
        $user_field->setDisableHtmlAutoComplete(false);
        $user_field->setDataSource(
            $this->ctrl->getLinkTargetByClass(
                self::class,
                self::CMD_PARTICIPANT_SEARCH
            )
        );

        $submit_button = ilSubmitButton::getInstance();
        $submit_button->setCaption($this->txt('add_participant'), false);
//        $submit_button->setCommand(
//            $this->ctrl->getLinkTargetByClass(
//                self::class,
//                self::CMD_PARTICIPANT_ADD
//            )
//        );

        $this->toolbar->addInputItem($user_field);
        $this->toolbar->addButtonInstance($submit_button);

        $table_gui = new ilObjSrVideoInterviewParticipantTableGUI($this, self::CMD_PARTICIPANT_INDEX);
        $table_gui->setData($this->repository->getParticipantsByObjId($this->obj_id));

        $this->tpl->setContent($table_gui->getHTML());
    }

    /**
     * addParticipant()s key-autocomplete ajax data source
     */
    protected function searchParticipant() : void
    {
        $term = filter_input(INPUT_GET, "term");
        $users = array();
        foreach (ilObjUser::searchUsers($term) as $user) {
            $users[$user['usr_id']] = "{$user['firstname']}, {$user['lastname']} [{$user['login']} | {$user['usr_id']}]";
        }

        if (empty($users)) {
            $users = array($this->txt('nothing_found'));
        }

        // since it's not working properly with http->response()
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($users);
        exit;
    }

    /**
     * add a new participant to a VideoInterview object.
     */
    protected function addParticipant() : void
    {


        $user_data = $this->http->request()->getParsedBody();

        print_r($user_data);
        exit;

        // match user_login without brackets from auto-completed string. (ugly)
        preg_match("/(?<=\[).+?(?=\])/", $user_data, $user_login);
        $user = ilObjUser::searchUsers($user_login[0])[0];

        $result = $this->repository->store(new Participant(
            null,
            0,
            0,
            $this->obj_id,
            $user['usr_id']
        ));

        if ($result) {
            ilUtil::sendSuccess($this->txt('participant_added'), true);
            $this->ctrl->redirectByClass(
                self::class,
                self::CMD_PARTICIPANT_INDEX
            );
        } else {
            // may show error toast or something here.
        }
    }

    protected function removeParticipant() : void
    {
        $participant_id = $this->http->request()->getQueryParams()['participant_id'];
        $participant = $this->repository->getParticipantById($participant_id);

        if (null !== $participant) {
            $this->repository->removeParticipantById($participant_id);
            ilUtil::sendSuccess($this->txt('participant_removed'), true);
            $this->ctrl->redirectByClass(
                self::class,
                self::CMD_PARTICIPANT_INDEX
            );
        } else {
            // may show error toast or something here.
        }
    }

    protected function notifyParticipants() : void
    {

    }
}