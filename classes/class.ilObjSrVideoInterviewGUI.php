<?php

//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewAnswerGUI.php";
//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewExerciseGUI.php";
//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantGUI.php";

use ILIAS\UI\Component\Input\Container\Form\Standard;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;

/**
 * Class ilObjSrVideoInterviewGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewExerciseGUI, ilObjSrVideoInterviewAnswerGUI, ilObjSrVideoInterviewParticipantGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    /**
     * Repository Object settings tab
     */
    const VIDEO_INTERVIEW_TAB = 'video_interview_tab';

    /**
     * Repository Object commands
     */
    const CMD_VIDEO_INTERVIEW_EDIT = 'editVideoInterview';
    const CMD_VIDEO_INTERVIEW_UPDATE = 'updateVideoInterview';

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
     * @TODO: implement and use a general repository with dependencies of the different repositories.
     *
     * @var ExerciseRepository
     */
    protected $repository;

    /**
     * Initialise ilObjSrVideoInterviewGUI
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        $this->repository  = new ExerciseRepository();
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
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $this->setupTabs(); // when using setTabs(), tabs cannot be activated.
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class) {
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
                // do nothing, let parent handle $next_class
                break;
        }

        parent::executeCommand();
    }

    /**
     * dispatches the given command and calls the corresponding method.
     * @param string $cmd
     */
    public function performCommand(string $cmd) : void
    {
        switch ($cmd) {
            case self::CMD_VIDEO_INTERVIEW_EDIT:
            case self::CMD_VIDEO_INTERVIEW_UPDATE:
                $this->tabs->activateTab(self::VIDEO_INTERVIEW_TAB);
                if ($this->access->checkAccess("write", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            default:
                // do nothing, let parent handle $cmd.
                break;
        }
    }

    /**
     * creates object tabs and links the corresponding plugin GUI classes.
     */
    protected function setupTabs() : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id)) {
            $this->tabs->addTab(
            // Exercise tab
                ilObjSrVideoInterviewExerciseGUI::EXERCISE_TAB,
                $this->txt(ilObjSrVideoInterviewExerciseGUI::EXERCISE_TAB),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewExerciseGUI::class,
                    ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX
                )
            );
        }

        if ($this->access->checkAccess("write", "", $this->ref_id)) {
            $this->tabs->addTab(
            // VideoInterview settings tab
                self::VIDEO_INTERVIEW_TAB,
                $this->txt(self::VIDEO_INTERVIEW_TAB),
                $this->ctrl->getLinkTargetByClass(
                    self::class,
                    self::CMD_VIDEO_INTERVIEW_EDIT,
                )
            );

            $this->tabs->addTab(
            // Participant tab
                ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB,
                $this->txt(ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewParticipantGUI::class,
                    ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX,
                )
            );
        }
    }

    /**
     * builds a standard form for the video interview repository object.
     *
     * @param array $values
     * @return Standard
     */
    protected function buildVideoInterviewForm(array $values = array(
        'title' => "",
        'description' => "",
        'exercise_detailed_description' => "",
        'exercise_resource' => "",
    )) : Standard
    {
        $video_interview_inputs = array(
            'title' => $this->ui_factory
                ->input()
                ->field()
                ->text($this->txt('title'))
                ->withValue($values['title'])
                ->withRequired(true)
                ->withAdditionalTransformation(
                    $this->refinery->custom()->transformation(
                        function (string $value) {
                            if (null !== $value) {
                                $this->object->setTitle($value);
                                return $value;
                            }

                            return null;
                        }
                    )
                )
            ,

            'description' => $this->ui_factory
                ->input()
                ->field()
                ->text($this->txt('description'))
                ->withValue($values['description'])
                ->withAdditionalTransformation(
                    $this->refinery->custom()->transformation(
                        function (string $value) {
                            $this->object->setDescription($value);
                            return $value;
                        }
                    )
                )
            ,
        );

        $exercise_inputs = array(
            'exercise_detailed_description' => $this->ui_factory
                ->input()
                ->field()
                ->textarea($this->txt('exercise_detailed_description'))
                ->withValue($values['exercise_detailed_description'])
                ->withRequired(true)
            ,

            'exercise_resource' => $this->ui_factory
                ->input()
                ->field()
                ->text($this->txt('exercise_resource'))
                ->withValue($values['exercise_resource'])
            ,
        );

        return $this->ui_factory
            ->input()
            ->container()
            ->form()
            ->standard(
                $this->ctrl->getFormActionByClass(
                    self::class,
                    self::CMD_VIDEO_INTERVIEW_UPDATE
                ),
                array_merge($video_interview_inputs, $exercise_inputs)
            )
        ;
    }

    /**
     * update VideoInterview repository object and exercise, using the VideoInterview
     * title and description.
     */
    protected function updateVideoInterview() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];
        $exercise = $this->repository->get($exercise_id);
        $form = $this->buildVideoInterviewForm()
            ->withRequest($this->http->request())
            ->withAdditionalTransformation(
                $this->refinery->custom()->transformation(
                    function ($data) {
                        if (null !== $data) {
                            $this->object->update();
                            return $data;
                        }

                        return null;
                    }
                )
            )
        ;

        if (null !== $exercise &&
            null !== ($data = $form->getData())
        ) {
            $exercise
                ->setTitle($this->object->getTitle())
                ->setDescription($this->object->getDescription())
                ->setDetailedDescription($data['exercise_detailed_description'])
                ->setResourceId($data['exercise_resource'])
            ;

            $this->repository->store($exercise);
            ilUtil::sendSuccess($this->txt('exercise_updated'), true);
            $this->ctrl->redirectByClass(
                self::class,
                self::CMD_VIDEO_INTERVIEW_EDIT
            );
        } else {
            $this->tpl->setContent(
                $this->ui_renderer->render($form)
            );
        }
    }

    /**
     * render the VideoInterview object form, including exercise data.
     */
    protected function editVideoInterview() : void
    {
        $exercise = $this->repository->getByObjId($this->obj_id)[0];
        $data = array(
            'title' => $this->object->getTitle(),
            'description' => $this->object->getDescription()
        );

        if (null !== $exercise) {
            $this->ctrl->setParameterByClass(
                self::class,
                'exercise_id',
                $exercise->getId()
            );

            $data['exercise_detailed_description'] = $exercise->getDetailedDescription();
            $data['exercise_resource'] = $exercise->getResourceId();
        }

        $this->tpl->setContent(
            $this->ui_renderer->render(
                $this->buildVideoInterviewForm($data)
            )
        );
    }

    /**
     * renders a "permission denied" error toast.
     */
    protected function permissionDenied() : void
    {
        // @TODO: implement permissionDenied() method.
    }
}
