<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantTableGUI.php";
require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use ILIAS\UI\Implementation\Component\Input\Field\MultiSelectUserInput;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;
use ILIAS\Filesystem\Stream\Streams;

/**
 * ilObjVideoInterviewParticipantGUI is the responsible class for managing a VideoInterview's participants.
 *
 * in general, this class displays a list of all participants and some of their stats like
 * if they've been invited or have answered an Exercise yet.
 * it also adds a toolbar from which participants can easily be added per search or role. it provides
 * a button to broadcast an invitation too, which will be sent internally and externally.
 *
 * @author            Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjVideoInterviewParticipantGUI: ilObjSrVideoInterviewGUI
 * @ilCtrl_Calls      ilObjVideoInterviewParticipantGUI: ilRepositorySearchGUI
 */
class ilObjSrVideoInterviewParticipantGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * @var string tab-name and translation-var
     */
    const PARTICIPANT_TAB = 'participant_tab';

    /**
     * Participant GUI commands
     */
    const CMD_PARTICIPANT_INDEX             = 'showAll';
    const CMD_PARTICIPANT_SEARCH            = 'searchParticipant';
    const CMD_PARTICIPANT_ADD               = 'addParticipant';
    const CMD_PARTICIPANT_ADD_BY_ROLE       = 'addParticipantsByRole';
    const CMD_PARTICIPANT_REMOVE            = 'removeParticipant';
    const CMD_PARTICIPANT_NOTIFY            = 'notifyParticipant';
    const CMD_PARTICIPANT_BROADCAST         = 'notifyAllParticipants';
    const CMD_PARTICIPANT_CONFIRM_BROADCAST = 'confirmBroadcast';

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * Initialise ilObjVideoInterviewParticipantGUI and load further dependencies.
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
        switch ($this->ctrl->getCmd(self::CMD_PARTICIPANT_INDEX)) {
            case self::CMD_PARTICIPANT_INDEX:
            case self::CMD_PARTICIPANT_SEARCH:
            case self::CMD_PARTICIPANT_ADD:
            case self::CMD_PARTICIPANT_ADD_BY_ROLE:
            case self::CMD_PARTICIPANT_REMOVE:
            case self::CMD_PARTICIPANT_NOTIFY:
            case self::CMD_PARTICIPANT_BROADCAST:
            case self::CMD_PARTICIPANT_CONFIRM_BROADCAST:
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

    /**
     * this method adds a toolbar to the page, from which users can be added as participants
     * and later invited via broadcast-method.
     */
    protected function setupToolbar() : void
    {
        // general toolbar options
        $this->toolbar->setFormAction($this->ctrl->getFormActionByClass(self::class));
        $this->toolbar->setPreventDoubleSubmission(true);

        // multi-select user search
        $multi_select_user_input = new MultiSelectUserInput("", "user_data");
        $multi_select_user_input->setDataSource(
            $this->ctrl->getLinkTargetByClass(
                self::class,
                self::CMD_PARTICIPANT_SEARCH,
                "",
                true
            )
        );

        $add_participants_btn = ilLinkButton::getInstance();
        $add_participants_btn->setCaption($this->txt('add_participants'), false);
        $add_participants_btn->setUrl($this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_PARTICIPANT_ADD
        ));

        $this->toolbar->addInputItem($multi_select_user_input);
        $this->toolbar->addButtonInstance($add_participants_btn);
        $this->toolbar->addSeparator();

        // search user by roles
        $add_by_role_btn = ilLinkButton::getInstance();
        $add_by_role_btn->setCaption($this->txt('add_participants_by_role'), false);
        $add_by_role_btn->setUrl($this->ctrl->getLinkTargetByClass(
            array(
                // delegate to ilRepositorySearchGUI
                self::class,
                ilRepositorySearchGUI::class
            )
        ));

        $this->toolbar->addButtonInstance($add_by_role_btn);
        $this->toolbar->addSeparator();

        // broadcast an invitation
        $broadcast_btn = ilLinkButton::getInstance();
        $broadcast_btn->setCaption($this->txt('send_invitation'), false);
        $broadcast_btn->setUrl($this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_PARTICIPANT_CONFIRM_BROADCAST
        ));

        $this->toolbar->addButtonInstance($broadcast_btn);
        $this->toolbar->addSeparator();
    }

    /**
     * returns a json-array that contains all users for a given search-term,
     * used as an ajax-autocomplete datasource by the toolbars multi-select-user-input.
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
            $this->http
                ->response()
                ->withBody(Streams::ofString(json_encode($users)))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
        );

        $this->http->sendResponse();
        $this->http->close();
    }

    /**
     * sends an email to an existing participant and returns occurred errors.
     *
     * @param Participant $participant
     * @return array
     */
    protected function sendMailToParticipant(Participant $participant) : array
    {
        // use logged-in user (actor) as sender
        $mail = new ilMail($this->user->getId());
        $user = new ilObjUser($participant->getUserId());

        $mail->setSaveInSentbox(true);
        return $mail->enqueue(
            $user->getLogin(),
            '',
            '',
            $this->txt('invitation_title'),
            $this->txt('invitation_message'),
            array()
        );
    }

    /**
     * adds new Participants to the current VideoInterview by an array of user-id's
     * if they don't exist and redirects on success/failure.
     *
     * @param array $user_ids
     */
    protected function addParticipantsForUserIds(array $user_ids) : void
    {
        if (!empty($user_ids)) {
            $errors = array();
            foreach ($user_ids as $user_id) {
                $participant = $this->repository->getParticipantForObjByUserId($this->obj_id, $user_id);
                if (null === $participant) {
                    $errors[] = $this->repository->store(new Participant(
                        null,
                        false,
                        $this->obj_id,
                        (int) $user_id
                    ));
                }
            }

            if (!in_array(false, $errors, true)) {
                ilUtil::sendSuccess($this->txt('participant_added'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_PARTICIPANT_INDEX
                );
            }
        }

        ilUtil::sendFailure($this->txt('general_error'), true);
        $this->ctrl->redirectByClass(
            self::class,
            self::CMD_PARTICIPANT_INDEX
        );
    }

    /**
     * displays a confirmation screen, before the broadcast-method is called.
     */
    protected function confirmBroadcast() : void
    {
        $confirmation_screen = new ilConfirmationGUI();
        $confirmation_screen->setFormAction($this->ctrl->getFormActionByClass(self::class));
        $confirmation_screen->setHeaderText($this->plugin->txt('confirm_send_invitation_text'));
        $confirmation_screen->setConfirm(
            $this->plugin->txt('confirm_send_invitation'),
            self::CMD_PARTICIPANT_BROADCAST
        );

        $confirmation_screen->setCancel(
            $this->plugin->txt('cancel'),
            self::CMD_PARTICIPANT_INDEX
        );

        $this->tpl->setContent($confirmation_screen->getHTML());
    }

    /**
     * displays all existing participants for the current VideoInterview object in a data-table.
     *
     * @see ilObjSrVideoInterviewParticipantTableGUI
     */
    protected function showAll() : void
    {
        $this->setupToolbar();
        $table_gui = new ilObjSrVideoInterviewParticipantTableGUI(
            $this, self::CMD_PARTICIPANT_INDEX
        );

        $this->tpl->setContent($table_gui->getHTML());
    }

    /**
     * adds new Participants to the current VideoInterview if they don't exist.
     * this is called by the toolbars multi-user-select-input.
     *
     * @see ilObjSrVideoInterviewParticipantGUI::setupToolbar()
     */
    protected function addParticipant() : void
    {
        $this->addParticipantsForUserIds(
            (array) $this->http->request()->getParsedBody()['selected']
        );
    }

    /**
     * adds new Participants by role to the current VideoInterview if they don't exist.
     * this is called by the ilRepositorySearchGUI class.
     *
     * @see ilRepositorySearchGUI::addUser()
     *
     * @param array $user_ids
     */
    public function addParticipantsByRole(array $user_ids) : void
    {
        $this->addParticipantsForUserIds($user_ids);
    }

    /**
     * removes an existing participant and all saved Answers for the current VideoInterview.
     */
    protected function removeParticipant() : void
    {
        $participant_id = $this->http->request()->getQueryParams()['participant_id'];
        $participant    = $this->repository->getParticipantById($participant_id);
        if (null !== $participant) {
            if ($this->repository->deleteAnswersForParticipant($participant_id) &&
                $this->repository->removeParticipantById($participant_id)
            ) {
                ilUtil::sendSuccess($this->txt('participant_removed'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_PARTICIPANT_INDEX
                );
            }
        }

        ilUtil::sendFailure($this->txt('general_error'), true);
        $this->ctrl->redirectByClass(
            self::class,
            self::CMD_PARTICIPANT_INDEX
        );
    }

    /**
     * sends an email to ONE existing Participant for the current VideoInterview, if
     * he haven't been invited yet.
     */
    protected function notifyParticipant() : void
    {
        $participant_id = $this->http->request()->getQueryParams()['participant_id'];
        $participant    = $this->repository->getParticipantById($participant_id);
        if (null !== $participant) {
            // skip notification when invitation has already been sent.
            if ($participant->isInvitationSent()) {
                ilUtil::sendFailure($this->txt('participant_already_invited'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_PARTICIPANT_INDEX
                );
            }

            if (empty($this->sendMailToParticipant($participant))) {
                $participant->setInvitationSent(true);
                $this->repository->store($participant);

                ilUtil::sendSuccess($this->txt('participant_invited'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_PARTICIPANT_INDEX
                );
            }
        }

        ilUtil::sendFailure($this->txt('general_error'), true);
        $this->ctrl->redirectByClass(
            self::class,
            self::CMD_PARTICIPANT_INDEX
        );
    }

    /**
     * sends an email to ALL existing Participants of the current VideoInterview, if
     * they haven't been invited yet.
     */
    protected function notifyAllParticipants() : void
    {
        $participants = $this->repository->getParticipantsByObjId($this->obj_id);
        if (null !== $participants) {
            $errors = array();
            foreach ($participants as $participant) {
                // skip notification if an invitation has already been sent.
                if (!$participant->isInvitationSent()) {
                    $error = $this->sendMailToParticipant($participant);
                    if (empty($error)) {
                        $participant->setInvitationSent(true);
                        $this->repository->store($participant);
                    } else {
                        $errors[] = $error;
                    }
                }
            }

            if (empty($errors)) {
                ilUtil::sendSuccess($this->txt('participant_invited'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_PARTICIPANT_INDEX
                );
            }
        }

        ilUtil::sendFailure($this->txt('general_error'), true);
        $this->ctrl->redirectByClass(
            self::class,
            self::CMD_PARTICIPANT_INDEX
        );
    }
}
