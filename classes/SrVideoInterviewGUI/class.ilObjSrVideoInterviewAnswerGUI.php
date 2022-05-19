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
 * @ilCtrl_isCalledBy ilObjVideoInterviewAnswerGUI: ilObjSrVideoInterviewGUI
 * @ilCtrl_Calls      ilObjVideoInterviewAnswerGUI: ilObjSrVideoInterviewParticipantGUI
 */
class ilObjSrVideoInterviewAnswerGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * Answer GUI commands
     */
    const CMD_ANSWER_SHOW     = 'showAnswer';
    const CMD_ANSWER_ADD      = 'addExerciseAnswer';
    const CMD_ANSWER_EVALUATE = 'addFeedbackAnswer';
    const CMD_ANSWER_PROCESS  = 'processAnswerForm';
    const CMD_ANSWER_DELETE   = 'deleteAnswer';

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
            case self::CMD_ANSWER_PROCESS:
                // @TODO: move this into method sub
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
                // @TODO: move this into method sub
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
        if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
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
        $inputs = array();
        if (ARAnswer::TYPE_ANSWER === $type) {
            $cmd = self::CMD_ANSWER_ADD;
            $inputs['answer_resource'] = VideoRecorderInput::getInstance(
                $this->video_upload_handler,
                $this->txt('answer') . " Video"
            );
        } else {
            $cmd = self::CMD_ANSWER_EVALUATE;
            $inputs['answer_content'] = $this->ui_factory
                ->input()
                ->field()
                ->textarea(
                    $this->txt('additional_content')
                );
        }

        return $this->ui_factory
            ->input()
            ->container()
            ->form()
            ->standard(
                $this->ctrl->getFormActionByClass(
                    self::class,
                    $cmd
                ),
                $inputs
            );
    }

    /**
     * get HTML markup to display an existing answer by it's id and show additional feedback.
     *
     * @param int      $answer_id
     * @param int|null $feedback_id
     * @return string
     * @throws ilTemplateException
     */
    public function getAnswerHTML(int $answer_id, int $feedback_id = null) : string
    {
        // @TODO: may implement this passively later.
        $answer = $this->repository->getAnswerById($answer_id);
        $participant = $this->repository->getParticipantById($answer->getParticipantId());

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

        if (null !== $feedback_id) {
            $feedback = $this->repository->getAnswerById($feedback_id);
            $tpl->addBlock('FEEDBACK_CONTENT_BLOCK', 'FEEDBACK_CONTENT_BLOCK', $this->ui_renderer->render(
                $this->ui_factory
                    ->legacy("
                            <div class=\"sr-feedback-wrapper\">
                                <h4>{$this->txt('feedback')}:</h4>
                                <p>{$feedback->getContent()}</p>
                                <br />
                            </div>
                        ")
            ));
        }

        // dont use strict comparison, only check for object property values
        if ($this->current_participant == $participant) {
            $tpl->addBlock("ANSWER_INFO_BLOCK", "ANSWER_INFO_BLOCK", $this->ui_renderer->render(
                $this->ui_factory
                    ->messageBox()
                    ->info(
                        (ARAnswer::TYPE_ANSWER === $answer->getType()) ?
                            $this->txt('already_answered') :
                            $this->txt('already_evaluated')
                    )
            ));
        }

        // dont use strict comparison, only check for property values
        if ($this->current_participant != $participant &&
            ARAnswer::TYPE_ANSWER === $answer->getType()
        ) {
            $this->ctrl->setParameterByClass(
                self::class,
                'exercise_id',
                $answer->getExerciseId()
            );

            $this->ctrl->setParameterByClass(
                self::class,
                'participant_id',
                $participant->getId()
            );

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

        return $tpl->get();
    }

    /**
     * show any type of answer
     *
     * @throws ilTemplateException
     */
    protected function showAnswer() : void
    {
        $answer_id = (int) $this->http->request()->getQueryParams()['answer_id'];

        if (null !== $answer_id &&
            null !== ($answer = $this->repository->getAnswerById($answer_id))
        ) {
            $this->tpl->setContent(
              $this->getAnswerHTML($answer->getId())
            );
        } else {
            $this->objectNotFound();
        }
    }

    /**
     * send an email to a Participant to inform him when a Feedback has been added.
     *
     * @param Participant $participant
     * @param int         $exercise_id
     * @return bool
     */
    protected function informParticipant(Participant $participant, int $exercise_id) : bool
    {
        $message = str_replace(
            '{GOTO_URL}',
            ilLink::_getStaticLink($this->ref_id) . "&exercise_id={$exercise_id}",
            $this->txt('new_feedback_message')
        );

        return empty($this->sendMailToUser(
            $participant->getUserId(),
            $this->txt('new_feedback_title'),
            $message
        ));
    }

    /**
     * add answer of any type
     *
     * @TODO: outsource this method into separate subs
     *
     * @param int $type
     * @throws ilTemplateException
     */
    protected function addAnswer(int $type) : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];
        $participant_id = (int) $this->http->request()->getQueryParams()['participant_id'];

        $participant = (ARAnswer::TYPE_FEEDBACK === $type) ?
            $this->repository->getParticipantById($participant_id) :
            $this->current_participant
        ;

        if (null !== $exercise_id &&
            null !== $participant
        ) {
            $answer = (ARAnswer::TYPE_ANSWER === $type) ?
                $this->repository->getParticipantAnswerForExercise($participant->getId(), $exercise_id) :
                $this->repository->getParticipantFeedbackForExercise($participant->getId(), $exercise_id)
            ;

            if (null === $answer) {
                $this->ctrl->setParameterByClass(
                    self::class,
                    'exercise_id',
                    $exercise_id
                );

                $this->ctrl->setParameterByClass(
                    self::class,
                    'participant_id',
                    $participant->getId()
                );

                $form = $this->getAnswerForm($type)->withRequest($this->http->request());
                $data = $form->getData();

                if (!empty($data['answer_resource']) ||
                    !empty($data['answer_content'])
                ) {
                    if (ARAnswer::TYPE_ANSWER === $type) {
                        $success_class = ilObjSrVideoInterviewExerciseGUI::class;
                        $success_cmd = ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
                        $failure_class = ilObjSrVideoInterviewExerciseGUI::class;
                        $failure_cmd = ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
                    } else {
                        $success_class = ilObjSrVideoInterviewParticipantGUI::class;
                        $success_cmd = ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX;
                        $failure_class = self::class;
                        $failure_cmd = self::CMD_ANSWER_SHOW;
                    }

                    if ($this->repository->store(new Answer(
                        null,
                        $type,
                        (string) $data['answer_content'],
                        (string) $data['answer_resource'],
                        '',
                        $exercise_id,
                        $participant->getId()
                    ))) {
                        if (ARAnswer::TYPE_FEEDBACK === $type) {
                            $lng_var = 'feedback_added';
                            $this->informParticipant($participant, $exercise_id);
                            $answer = $this->repository->getParticipantFeedbackForExercise(
                                $participant->getId(),
                                $exercise_id
                            );
                        } else {
                            $lng_var = 'answer_added';
                            $answer = $this->repository->getParticipantAnswerForExercise(
                                $participant->getId(),
                                $exercise_id
                            );
                        }

                        $this->ctrl->setParameterByClass(
                            self::class,
                            'answer_id',
                            $answer->getId()
                        );

                        ilUtil::sendSuccess($this->txt($lng_var), true);
                        $this->ctrl->redirectByClass(
                            $success_class,
                            $success_cmd
                        );
                    }

                    ilUtil::sendFailure($this->txt('general_error'), true);
                    $this->ctrl->redirectByClass(
                        $failure_class,
                        $failure_cmd
                    );
                }

                $this->tpl->setContent($this->custom_renderer->render(
                    $this->getAnswerForm($type)
                ));
            } else {
                $feedback = $this->repository->getParticipantFeedbackForExercise($participant->getId(), $exercise_id);
                $feedback_id = (null !== $feedback) ? $feedback->getId() : null;
                $this->tpl->setContent($this->getAnswerHTML($answer->getId(), $feedback_id));
            }
        } else {
            $this->objectNotFound();
        }
    }

    /**
     * delete any type of answer
     */
    protected function deleteAnswer() : void
    {
        $answer_id = $this->http->request()->getQueryParams()['answer_id'];
        $answer    = $this->repository->getAnswerById($answer_id);

        if (null !== $answer &&
            $this->repository->deleteAnswerById($answer_id)
        ) {
            ilUtil::sendSuccess($this->txt('answer_removed'), true);
            $this->ctrl->redirectByClass(
                ilObjSrVideoInterviewParticipantGUI::class,
                ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX
            );
        }

        ilUtil::sendFailure($this->txt('general_error'), true);
        $this->ctrl->redirectByClass(
            ilObjSrVideoInterviewParticipantGUI::class,
            ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX
        );
    }

    /**
     * add answer type FEEDBACK
     */
    protected function addFeedbackAnswer() : void
    {
        $this->addAnswer(ARAnswer::TYPE_FEEDBACK);
    }

    /**
     * add answer type ANSWER
     */
    protected function addExerciseAnswer() : void
    {
        if (null !== $this->current_participant) {
            $this->addAnswer(ARAnswer::TYPE_ANSWER);
        } else {
            $this->permissionDenied();
        }
    }
}