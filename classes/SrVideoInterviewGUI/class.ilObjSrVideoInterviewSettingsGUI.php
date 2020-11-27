<?php

require_once __DIR__ . "/../class.ilSrPermissionDeniedException.php";

use ILIAS\UI\Component\Input\Container\Form\Standard;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;

/**
 * Class ilObjSrVideoInterviewSettingsGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewSettingsGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewSettingsGUI
{
    const TAB_NAME  = 'xvin_tab_settings';

    const CMD_SETTINGS_SHOW  = 'showSettings';
    const CMD_SETTINGS_PROCESS = 'processSettings';

    /**
     * Exercise ID for texting purposes.
     *
     * @var int TEST_ID
     */
    const TEST_ID = 3;

    /**
     * @var int
     */
    private $ref_id;

    /**
     * @var int
     */
    private $obj_id;

    /**
     * @var ilTemplate
     */
    private $tpl;

    /**
     * @var ilTabsGUI
     */
    private $tabs;

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    private $http;

    /**
     * @var ilCtrl
     */
    private $ctrl;

    /**
     * @var ilLanguage
     */
    private $lang;

    /**
     * @var ilAccessHandler
     */
    private $access;

    /**
     * @var \ILIAS\UI\Factory
     */
    private $ui_factory;

    /**
     * @var \ILIAS\UI\Renderer
     */
    private $ui_renderer;

    /**
     * @var array
     */
    private $translations;

    /**
     * @var \ILIAS\Refinery\Factory
     */
    private $refinery;

    /**
     * @var ExerciseRepository
     */
    private $repository;

    public function __construct(int $ref_id, int $obj_id)
    {
        global $DIC;

        $this->translations = ilSrVideoInterviewPlugin::$translations;
        $this->repository = new ExerciseRepository();
        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->tpl      = $DIC->ui()->mainTemplate();
        $this->access   = $DIC->access();
        $this->tabs     = $DIC->tabs();
        $this->http     = $DIC->http();
        $this->ctrl     = $DIC->ctrl();
        $this->lang     = $DIC->language();
        $this->refinery = $DIC->refinery();
        $this->ref_id   = $ref_id;
        $this->obj_id   = $obj_id;
    }

    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::TAB_NAME);
        $cmd = $this->ctrl->getCmd(
            self::CMD_SETTINGS_SHOW
        );

        if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
            $this->$cmd();
        } else {
            throw new ilSrPermissionDeniedException();
        }
    }

    /**
     * build form for exercise entity
     *
     * @return Standard
     */
    private function buildSettingsForm(array $args = ['title' => "", 'description' => "", 'detail' => ""]) : Standard
    {
        $title = $this->ui_factory->input()->field()->text(
            $this->translations['title'],
        )->withValue($args['title']);

        $description = $this->ui_factory->input()->field()->textarea(
            $this->translations['description'],
        )->withValue($args['description']);

        $detailed_description = $this->ui_factory->input()->field()->textarea(
            $this->translations['detailed_description'],
        )->withValue($args['detail']);

        return $this->ui_factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTargetByClass(self::class, self::CMD_SETTINGS_PROCESS),
            [
                'title' => $title,
                'description' => $description,
                'detail' => $detailed_description
            ]
        );
    }

    /**
     * @return \ILIAS\Refinery\Custom\Transformation
     */
    private function getRefinery() : \ILIAS\Refinery\Custom\Transformation
    {
        return $this->refinery->custom()->transformation(function ($data) {
            return new Exercise(
                null,
                $data['title'],
                $data['description'],
                $data['detail'],
                "",
                $this->obj_id
            );
        });
    }

    /**
     * display form for an existing exercise entity.
     */
    private function processSettings() : void
    {
        $exercise_id = (int) $this->http->request()->getQueryParams()['exercise_id'];
        if (null !== $this->repository->get($exercise_id)) {
            $form = $this->buildSettingsForm()->withAdditionalTransformation(
                $this->getRefinery()
            )->withRequest($this->http->request());

            $this->repository->store($form->getData()->setId($exercise_id));
            $this->tpl->setContent(
                $this->ui_renderer->render($form)
            );
        } else {
            // display error toast instead.
            $this->showErrorToast($this->translations['exercise_not_found']);
        }
    }

    /**
     * process exercise form submissions and manipulate data.
     */
    private function showSettings() : void
    {
        $exercise = $this->repository->get(self::TEST_ID); // @TODO: use $repository->getByObjId instead.
        if (null !== $exercise) {
            $this->ctrl->setParameterByClass(self::class, "exercise_id", $exercise->getId());
            $this->tpl->setContent(
                $this->ui_renderer->render($this->buildSettingsForm([
                    'title' => (string) $exercise->getTitle(),
                    'description' => (string) $exercise->getDescription(),
                    'detail' => (string) $exercise->getDetailedDescription()
                ]))
            );
        } else {
            // display error toast instead.
            $this->showErrorToast($this->translations['exercise_not_found']);
        }
    }

    private function showErrorToast(string $msg) : void
    {
        $this->tpl->setContent(
            $this->ui_renderer->render(
                $this->ui_factory->messageBox()->failure($msg)
            )
        );
    }
}