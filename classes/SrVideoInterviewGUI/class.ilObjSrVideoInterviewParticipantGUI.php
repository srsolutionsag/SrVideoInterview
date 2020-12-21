<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantTableGUI.php";
require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

//use srag\CustomInputGUIs\TextInputGUI\TextInputGUIWithModernAutoComplete;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\UI\Implementation\Component\Input\Field\MultiSelectUserInput;

/**
 * Class ilObjVideoInterviewParticipantGUI
 * @author            Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjVideoInterviewParticipantGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewParticipantGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * Participant GUI tab-names and translation var
     */
    const PARTICIPANT_TAB = 'participant_tab';

    /**
     * Participant GUI commands
     */
    const CMD_PARTICIPANT_INDEX  = 'showAll';
    const CMD_PARTICIPANT_ADD    = 'addParticipant';
    const CMD_PARTICIPANT_REMOVE = 'removeParticipant';
    const CMD_PARTICIPANT_NOTIFY = 'notifyParticipants';
    const CMD_PARTICIPANT_SEARCH = 'searchParticipant';
    const CMD_PARTICIPANT_RESPOND = 'respondToParticipant';

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * Initialise ilObjVideoInterviewParticipantGUI
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

        switch ($cmd) {
            case self::CMD_PARTICIPANT_INDEX:
            case self::CMD_PARTICIPANT_ADD:
            case self::CMD_PARTICIPANT_REMOVE:
            case self::CMD_PARTICIPANT_NOTIFY:
            case self::CMD_PARTICIPANT_SEARCH:
            case self::CMD_PARTICIPANT_RESPOND:
                if ($this->access->checkAccess("write", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            default:
                $this->objectNotFound();
                break;
        }
    }

    protected function showAll() : void
    {
        $b = new MultiSelectUserInput("", "user_data");
        $b->setDataSource(
            $this->ctrl->getLinkTargetByClass(
                self::class,
                self::CMD_PARTICIPANT_SEARCH,
                "",
                true
            )
        );

        $this->toolbar->setFormAction($this->ctrl->getFormAction($this));
        $this->toolbar->addInputItem($b);
        $this->toolbar->setPreventDoubleSubmission(true);

        $table_gui = new ilObjSrVideoInterviewParticipantTableGUI($this, self::CMD_PARTICIPANT_INDEX);
        $table_gui->setData($this->repository->getParticipantsByObjId($this->obj_id));
        $this->tpl->setContent($table_gui->getHTML());
    }

    /**
     * addParticipant()s key-autocomplete ajax data source
     */
    protected function searchParticipant() : void
    {
        $term  = filter_input(INPUT_GET, "term");
        $users = array();
        foreach (ilObjUser::searchUsers($term) as $user) {
            $users[] = array(
                'label' => "{$user['firstname']} {$user['lastname']} [{$user['login']}]",
                'value' => $user['usr_id'],
            );
        }

        if (empty($users)) {
            $users = array(
                'label' => $this->txt('nothing_found'),
                'value' => "",
            );
        }

        $this->http->saveResponse(
            $this->http->response()
                       ->withBody(Streams::ofString(json_encode($users)))
                       ->withHeader('Content-Type', 'application/json; charset=utf-8')
        );
        $this->http->sendResponse();
        $this->http->close();
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
        $participant    = $this->repository->getParticipantById($participant_id);

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

    protected function respondToParticipant() : void
    {

    }

    protected function notifyParticipants() : void
    {

    }
}
