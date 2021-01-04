<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Answer;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;
use srag\Plugins\SrVideoInterview\AREntity\ARAnswer;

/**
 * ilObjSrVideoInterviewAnswerGUI is responsible for managing Participant Answers.
 *
 * this class is managing Participant Answers and Feedbacks, which both are types of Answers.
 * it adds a Participants Answer for an Exercise and let's the professor evaluate them, by
 * giving a Feedback to the Participant Answer.
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @TODO: work in progress.
 *
 * @ilCtrl_isCalledBy ilObjVideoInterviewAnswerGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewAnswerGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * Answer GUI commands
     */
    const CMD_ANSWER_SHOW     = 'showAnswer';
    const CMD_ANSWER_ADD      = 'addAnswer';
    const CMD_ANSWER_DELETE   = 'deleteAnswer';
    const CMD_ANSWER_EVALUATE = 'evaluateAnswer';

    /**
     * @var Participant|null
     */
    protected $current_participant;

    /**
     * Initialise ilObjVideoInterviewAnswerGUI an load further dependencies.
     *
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * load further dependencies that depend on the initialised parent.
     */
    protected function afterConstructor() : void
    {
        $this->current_participant = $this->repository->getParticipantForObjByUserId($this->obj_id, $this->user->getId());
    }

    /**
     * dispatches the given command and calls the corresponding method.
     */
    public function executeCommand() : void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_ANSWER_SHOW);
        switch ($cmd)
        {
            case self::CMD_ANSWER_SHOW:
            case self::CMD_ANSWER_ADD:
                $this->setupBackToTab($this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewExerciseGUI::class,
                    ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX
                ));
                if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            case self::CMD_ANSWER_DELETE:
            case self::CMD_ANSWER_EVALUATE:
                $this->setupBackToTab($this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewParticipantGUI::class,
                    ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX
                ));
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
     * replaces all tabs of the parent-object and adds a back to tab to return
     * to a given command.
     *
     * @param string $cmd
     *
     * @see ilObjSrVideoInterviewGUI::setupTabs()
     */
    protected function setupBackToTab(string $cmd) : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id)) {
            $this->tabs->clearTargets();
            // deactivate all other tabs that might be active
            $this->tabs->activateTab("should not be an actual id :)");
            $this->tabs->setBackTarget(
                $this->txt('back_to'),
                $cmd
            );
        }
    }

    /**
     * retrieve an Answer Standard form for the given answer type.
     *
     * @TODO: might add validation per transformation?
     *
     * @param int $type
     * @return Standard
     */
    protected function getAnswerForm(int $type) : Standard
    {
        return $this->ui_factory
            ->input()
            ->container()
            ->form()
            ->standard(
                $this->ctrl->getFormActionByClass(
                    self::class,
                    (ARAnswer::TYPE_ANSWER === $type) ?
                        self::CMD_ANSWER_ADD :
                        self::CMD_ANSWER_EVALUATE
                ),
                array(
                    'answer_resource' => VideoRecorderInput::getInstance(
                        $this->video_upload_handler,
                        $this->txt('answer') . " Video"
                    ),

                    'answer_content' => $this->ui_factory
                        ->input()
                        ->field()
                        ->textarea(
                            $this->txt('additional_content')
                        )
                    ,
                )
            );
    }

    /**
     * show any type of answer
     *
     * @throws ilTemplateException
     */
    protected function showAnswer() : void
    {
        $answer_id = (int) $this->http->request()->getQueryParams()['answer_id'];

        if (null !== ($answer = $this->repository->getAnswerById($answer_id)) &&
            null !== ($participant = $this->repository->getParticipantById($answer->getParticipantId()))
        ) {
            $this->ctrl->setParameterByClass(
                self::class,
                'exercise_id',
                $answer->getExerciseId()
            );

            $this->ctrl->setParameterByClass(
                self::class,
                'answer_id',
                $answer_id
            );

            // @TODO: might catch exception here?
            $tpl  = new ilTemplate(self::TEMPLATE_DIR . 'tpl.answer.html', false, false);
            $user = new ilObjUser($participant->getUserId());

            $title = "[{$user->getLogin()}] {$user->getFirstname()} {$user->getLastname()}'s ";
            $title.= (ARAnswer::TYPE_ANSWER === $answer->getType()) ?
                $this->txt('answer') :
                $this->txt('feedback')
            ;

            $tpl->setVariable('TITLE', $title);

            $tpl->setVariable('VIDEO', $this->getRecordedVideoHTML($answer->getResourceId()));

            if (!empty($answer->getContent())) {
                $tpl->addBlock('ANSWER_CONTENT_BLOCK', 'ANSWER_CONTENT_BLOCK', $this->ui_renderer->render(
                    $this->ui_factory
                        ->legacy("
                            <div>
                                <h4>{$this->txt('additional_content')}</h4>
                                <p>{$answer->getContent()}</p>
                                <br />
                            </div>
                        ")
                ));
            }

            // dont use strict comparison, only check for property values
            if ($this->current_participant == $participant) {
                $tpl->addBlock("ANSWER_INFO_BLOCK", "ANSWER_INFO_BLOCK", $this->ui_renderer->render(
                    $this->ui_factory
                        ->messageBox()
                        ->info(
                            $this->txt('already_answered')
                        )
                ));
            }

            if ($this->current_participant != $participant) {
                $tpl->setVariable("ACTION", $this->ui_renderer->render(
                    $this->ui_factory
                        ->button()
                        ->primary(
                            $this->txt('evaluate'),
                            $this->ctrl->getLinkTargetByClass(
                                self::class,
                                self::CMD_ANSWER_EVALUATE
                            )
                        )
                ));
            }

            $this->tpl->setContent($tpl->get());
        }

        $this->objectNotFound();
    }

    /**
     * add answer of type ANSWER
     */
    protected function addAnswer() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];

        if (null !== $exercise_id &&
            null !== $this->current_participant && !$this->repository->hasParticipantAnsweredExercise($this->current_participant->getId(), $exercise_id)
        ) {
            $form = $this->getAnswerForm(ARAnswer::TYPE_ANSWER)->withRequest($this->http->request());
            $data = $form->getData();

            if (!empty($data)) {
                // process and add new
                if ($this->repository->store(new Answer(
                    null,
                    ARAnswer::TYPE_ANSWER,
                    ''
                ))) {
                    // send success
                }

                // send failure
            }

            $this->tpl->setContent($this->ui_renderer->render($form));
        }

        $this->objectNotFound();
    }

    /**
     * add answer type FEEDBACK
     */
    protected function evaluateAnswer() : void
    {

    }

    /**
     * delete any type of answer
     */
    protected function deleteAnswer() : void
    {

    }
}