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
 * this class manages Participant Answers to Exercises, which also contains a professors
 * feedback (Answer) to a Participants Answer.
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @TODO: may refactor professor/participant Answer view.
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
    const CMD_ANSWER_SHOW_TUT = 'showAnswerForEvaluation';

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
        $this->setupBackToTab();
        $this->tabs->activateTab("should_not_be_an_actual_id");
        $cmd = $this->ctrl->getCmd(self::CMD_ANSWER_SHOW);

        switch ($cmd)
        {
            case self::CMD_ANSWER_SHOW:
            case self::CMD_ANSWER_ADD:
                if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            case self::CMD_ANSWER_DELETE:
            case self::CMD_ANSWER_EVALUATE:
            case self::CMD_ANSWER_SHOW_TUT:
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
     * replaces all tabs of the parent-object and adds a back-to tab.
     *
     * @see ilObjSrVideoInterviewGUI::setupTabs()
     */
    protected function setupBackToTab() : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id)) {
            $this->tabs->clearTargets();
            $this->tabs->setBackTarget(
                $this->txt('back_to'),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewExerciseGUI::class,
                    ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX
                )
            );
        }
    }

    /**
     * renders an Answer and adds it to the main template.
     *
     * @param Answer $answer
     * @throws ilTemplateException
     */
    protected function renderAnswer(Answer $answer) : void
    {
        $tpl = new ilTemplate(self::TEMPLATE_DIR . 'tpl.answer.html', false, false);

        $participant = $this->repository->getParticipantById($answer->getParticipantId());
        $user = new ilObjUser($participant->getUserId());

        $tpl->setVariable('TITLE', "[{$user->getLogin()}] {$user->getFirstname()} {$user->getLastname()}'s {$this->txt('answer')}");
        $tpl->setVariable('VIDEO', $this->getRecordedVideoHTML($answer->getResourceId()));

        if ('' !== $answer->getContent()) {
            $tpl->addBlock("ANSWER_CONTENT_BLOCK", "ANSWER_CONTENT_BLOCK", "
                <div>
                    <h4>{$this->txt('additional_content')}</h4>
                    <p>{$answer->getContent()}</p>
                    <br />
                </div>
            ");
        }

        if ($this->current_participant !== $participant) {
            $tpl->addBlock("ANSWER_INFO_BLOCK", "ANSWER_INFO_BLOCK", $this->ui_renderer->render(
                $this->ui_factory
                    ->messageBox()
                    ->info(
                        $this->txt('already_answered')
                    )
            )
            );
        }

        $this->tpl->setContent($tpl->get());
    }

    /**
     * builds and returns the Answer form with corresponding input-fields.
     *
     * @return Standard
     */
    protected function buildAnswerForm() : Standard {
        return $this->ui_factory
            ->input()
            ->container()
            ->form()
            ->standard(
                $this->ctrl->getFormActionByClass(
                    self::class,
                    self::CMD_ANSWER_ADD
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
     * displays an existing answer for the professor to evaluate.
     *
     * @throws ilTemplateException
     */
    protected function showAnswerForEvaluation() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];
        $participant_id = (int) $this->http->request()->getQueryParams()['participant_id'];

        if (null !== $exercise_id &&
            null !== $participant_id
        ) {
           if (null !== ($answer = $this->repository->getParticipantAnswerForExercise($participant_id, $exercise_id))) {
               $this->renderAnswer($answer);
           }
        }

        $this->objectNotFound();
    }

    /**
     * displays the Answer form for a Participant.
     *
     * @throws ilTemplateException
     */
    protected function showAnswer() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];
        if (null !== $exercise_id &&
            null !== $this->current_participant) {
            if (!$this->repository->hasParticipantAnsweredExercise(
                $this->current_participant->getId(),
                $exercise_id
            )) {
                $this->ctrl->setParameterByClass(
                    self::class,
                    "exercise_id",
                    $exercise_id
                );

                $this->tpl->setContent(
                   $this->ui_renderer->render(
                       $this->buildAnswerForm()
                   )
                );
            } else {
                $this->renderAnswer($this->repository->getParticipantAnswerForExercise(
                    $this->current_participant->getId(),
                    $exercise_id
                ));
            }
        }

        $this->objectNotFound();
    }

    /**
     * adds a Participants Answer for the current Exercise.
     */
    protected function addAnswer() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];

        if (null !== $exercise_id &&
            null !== $this->current_participant
        ) {
            $form = $this->buildAnswerForm()->withRequest($this->http->request());
            $data = $form->getData();

            if (!empty($data['answer_resource'])) {
                $this->repository->store(new Answer(
                    null,
                    ARAnswer::TYPE_ANSWER,
                    (string) $data['answer_content'],
                    $data['answer_resource'],
                    $exercise_id,
                    $this->current_participant->getId()
                ));

                ilUtil::sendSuccess($this->txt('exercise_answered'), true);
                $this->ctrl->redirectByClass(
                    ilObjSrVideoInterviewExerciseGUI::class,
                    ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX
                );
            } else {
                ilUtil::sendFailure($this->txt('answer_not_completed'), true);
                $this->ctrl->redirectByClass(
                    self::class,
                    self::CMD_ANSWER_SHOW
                );
            }
        } else {
            $this->objectNotFound();
        }
    }

    /**
     * deletes an existing answer or feedback of a Participant and the current Exercise.
     *
     * @TODO: implement method.
     */
    protected function deleteAnswer() : void
    {

    }

    /**
     * adds a feedback to a Participants Answer for the current Exercise.
     *
     * @TODO: implement method
     */
    protected function evaluateAnswer() : void
    {

    }
}