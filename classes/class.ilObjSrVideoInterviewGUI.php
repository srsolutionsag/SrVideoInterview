<?php

use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\MainMenu\Storage\Services;

/**
 * ilObjSrVideoInterviewGUI in general, dispatches a request's next class and command and delegates it accordingly.
 *
 * this class is also used as a parent class to all other GUI classes of this plugin. This way, we can share
 * common dependencies and provide our children with useful helper functions such as permissionDenied() and
 * objectNotFound().
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @author            Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewExerciseGUI, ilObjSrVideoInterviewAnswerGUI, ilObjSrVideoInterviewParticipantGUI, ilRepositorySearchGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    /**
     * @var string plugin template dir
     */
    const TEMPLATE_DIR = './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/default/';
    const CSS_DIR      = './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/css/default/';
    const JS_DIR       = './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/default/';

    /**
     * @var string repository object tab (must be called settings, to work properly with parent commands)
     */
    const VIDEO_INTERVIEW_TAB = 'settings';

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
     * @var ilObjSrVideoInterviewUploadHandlerGUI
     */
    protected $video_upload_handler;

    /**
     * @var ilObjUser
     */
    protected $user;

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

        $this->video_upload_handler = new ilObjSrVideoInterviewUploadHandlerGUI();
        $this->repository  = new VideoInterviewRepository();
        $this->storage     = new Services();
        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->refinery    = $DIC->refinery();
        $this->http        = $DIC->http();
        $this->user        = $DIC->user();

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
        return ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX;
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
     * In this version of the plugin we create one excercise for one object and
     * therefore the excercise is created here in afterSave.
     *
     * @param ilObject $newObj
     */
    public function afterSave(ilObject $newObj)
    {
        $form = $this->initCreateForm($newObj->getType());
        $form->checkInput();
        $exercise = new Exercise(
            null,
            $newObj->getTitle(),
            $newObj->getDescription(),
            $form->getInput('exercise_detailed_description'),
            "",
            $newObj->getId()
        );

        $this->repository->store($exercise);

        parent::afterSave($newObj);
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
            case '':
            case strtolower(ilObjSrVideoInterviewExerciseGUI::class):
                if (!$this->getCreationMode()) {
                    $exercise_gui = new ilObjSrVideoInterviewExerciseGUI($this->ref_id);
                    $this->ctrl->forwardCommand($exercise_gui);
                }
                break;
            case strtolower(ilObjSrVideoInterviewUploadHandlerGUI::class):
                $this->ctrl->forwardCommand($this->video_upload_handler);
                break;
            case strtolower(ilObjSrVideoInterviewAnswerGUI::class):
                $answer_gui = new ilObjSrVideoInterviewAnswerGUI($this->ref_id);
                $this->ctrl->forwardCommand($answer_gui);
                break;
            case strtolower(ilObjSrVideoInterviewParticipantGUI::class):
                $participant_gui = new ilObjSrVideoInterviewParticipantGUI($this->ref_id);
                $this->ctrl->forwardCommand($participant_gui);
                break;
            case strtolower(ilRepositorySearchGUI::class):
                $this->tabs->clearTargets();
                $this->tabs->setBackTarget($this->plugin->txt('back_to'), $this->ctrl->getLinkTargetByClass(ilObjSrVideoInterviewParticipantGUI::class));
                $search = new ilRepositorySearchGUI();
                $participant_gui = new ilObjSrVideoInterviewParticipantGUI($this->ref_id);
                $search->setCallback($participant_gui, ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_ADD_BY_ROLE);
                $this->ctrl->forwardCommand($search);
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
                $this->objectNotFound();
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

    public function initCreateForm($a_new_type)
    {
        $form                          = parent::initCreateForm($a_new_type);
        $exercise_detailed_description = new ilTextAreaInputGUI($this->txt('exercise_detailed_description'), 'exercise_detailed_description');
        $form->addItem($exercise_detailed_description);

        return $form;
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

            'exercise_resource' => VideoRecorderInput::getInstance(
                $this->video_upload_handler,
                'Video'
            )->withValue($values['exercise_resource']),
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
                ->setResourceId($data['exercise_resource'] ?? "")
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
     * returns html needed to display an existing video File resource.
     *
     * @param string $resource_id
     * @return string
     */
    protected function getRecordedVideoHTML(string $resource_id) : string
    {
        $download_url = $this->video_upload_handler->getExistingFileDownloadURL();
        $file_identifier_key = $this->video_upload_handler->getFileIdentifierParameterName();

        $this->tpl->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/css/default/UIComponent/style.video_recorder_input.css");

        return "<div class=\"sr-video-wrapper\"><video src=\"{$download_url}&{$file_identifier_key}={$resource_id}\" controls playsinline></video></div>";
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
