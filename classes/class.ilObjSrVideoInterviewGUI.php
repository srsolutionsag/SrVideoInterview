<?php

//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewAnswerGUI.php";
//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewExerciseGUI.php";
//require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantGUI.php";

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;
use ILIAS\MainMenu\Storage\Services;

/**
 * ilObjSrVideoInterviewGUI in general, dispatches a request's next class and command and delegates it accordingly.
 *
 * this class is also used as a parent class to all other GUI classes of this plugin. This way, we can share
 * common dependencies and provide our children with useful helper functions such as permissionDenied() and
 * objectNotFound(), which display a toast-message.
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewExerciseGUI, ilObjSrVideoInterviewAnswerGUI, ilObjSrVideoInterviewParticipantGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    /**
     * Repository Object tab (must be named settings, in order to work properly with parent commands below)
     */
    const VIDEO_INTERVIEW_TAB        = 'settings';

    /**
     * Repository Object commands (replace by parent methods when implementing m:1)
     *
     * @see ilObjectGUI::editObject()
     * @see ilObjectGUI::updateObject()
     */
    const CMD_VIDEO_INTERVIEW_EDIT   = 'editVideoInterview';
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
     * @var Services
     */
    protected $storage;

    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;

    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;

    /**
     * @var VideoInterviewRepository
     */
    protected $repository;

    /**
     * Initialise ilObjSrVideoInterviewGUI and declare further dependencies.
     *
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        $this->repository  = new VideoInterviewRepository();
        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->refinery    = $DIC->refinery();
        $this->http        = $DIC->http();
        $this->storage     = new Services();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * @inheritDoc
     * @return string
     */
    final public function getType() : string
    {
        return ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * @inheritDoc
     * @return string
     */
    final public function getAfterCreationCmd() : string
    {
        return ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_ADD;
    }

    /**
     * @inheritDoc
     * @return string
     */
    final public function getStandardCmd() : string
    {
        return ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
    }

    /**
     * dispatches a requests next class and delegates it to the responsible class.
     *
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $this->setupTabs(); // when using setTabs(), tabs cannot be activated.
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class) {
            case strtolower(ilObjSrVideoInterviewUploadHandlerGUI::class):
                $this->ctrl->forwardCommand(new ilObjSrVideoInterviewUploadHandlerGUI());
                break;
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
     * dispatches repository object commands and calls the responsible parent-method.
     *
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
                // we should not reach this.
                break;
        }
    }

    /**
     * adds our repository object tabs according to a users permission.
     */
    final protected function setupTabs() : void
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
                    self::CMD_VIDEO_INTERVIEW_EDIT
                )
            );

            $this->tabs->addTab(
            // Participant tab
                ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB,
                $this->txt(ilObjSrVideoInterviewParticipantGUI::PARTICIPANT_TAB),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewParticipantGUI::class,
                    ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX
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
//
//            'test' => $this->ui_factory->input()->field()->file(
//                new ilObjSrVideoInterviewUploadHandlerGUI(),
//                "test"
//            ),

            'exercise_resource' => VideoRecorderInput::getInstance(
                new ilObjSrVideoInterviewUploadHandlerGUI(),
                'Video',
                'exercise_resource'
            ),
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
     *
     * @TODO: move to ExerciseGUI when implementing m:1.
     */
    protected function updateVideoInterview() : void
    {
        // assuming we use 1:1 cardinality and can only retrieve one object yet
        $exercise = $this->repository->getExercisesByObjId($this->obj_id)[0];
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
//                ->setResourceId($data['exercise_resource'])
            ;

            $this->repository->store($exercise);
            ilUtil::sendSuccess($this->txt('exercise_updated'), true);
            $this->ctrl->redirectByClass(
                self::class,
                self::CMD_VIDEO_INTERVIEW_EDIT
            );
        }

        $this->tpl->setContent(
            $this->ui_renderer->render($form)
        );
    }

    /**
     * render the VideoInterview object form, including exercise data.
     *
     * @TODO: move to ExerciseGUI when implementing m:1
     */
    protected function editVideoInterview() : void
    {
        // assuming we use 1:1 cardinality and can only retrieve one object yet
        $exercise = $this->repository->getExercisesByObjId($this->obj_id)[0];
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

    /**
     * renders a "object not found" error toast.
     */
    protected function objectNotFound() : void
    {
        // @TODO: implement objectNotFound() method.
    }
}
