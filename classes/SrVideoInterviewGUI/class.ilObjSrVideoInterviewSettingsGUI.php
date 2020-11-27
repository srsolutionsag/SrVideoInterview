<?php

require_once __DIR__ . "/../class.ilSrPermissionDeniedException.php";
require_once __DIR__ . "/../class.ilObjSrVideoInterviewGUI.php";

use ILIAS\UI\Component\Input\Container\Form\Standard;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;

/**
 * Class ilObjSrVideoInterviewSettingsGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewSettingsGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewSettingsGUI extends ilObjSrVideoInterviewGUI
{
    const TAB_NAME             = 'xvin_tab_settings';
    const CMD_SETTINGS_SHOW    = 'showSettings';
    const CMD_SETTINGS_PROCESS = 'processSettings';

    // for testing purposes only.
    const TEST_ID = 1;

    /**
     * @var ExerciseRepository
     */
    protected $repository;

    /**
     * Initialise ilObjSrVideoInterviewSettingsGUI
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        $this->repository = new ExerciseRepository();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * @inheritDoc
     */
    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::TAB_NAME);
        $cmd = $this->ctrl->getCmd(self::CMD_SETTINGS_SHOW);
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
            $this->txt('title')
        )->withValue($args['title']);

        $description = $this->ui_factory->input()->field()->textarea(
            $this->txt('description')
        )->withValue($args['description']);

        $detailed_description = $this->ui_factory->input()->field()->textarea(
            $this->txt('xvin_detailed_description')
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
                $this->renderSuccessMessage($this->txt('xvin_exercise_updated')) .
                $this->ui_renderer->render($form)
            );
        } else {
            $this->tpl->setContent(
                $this->renderErrorMessage($this->txt('xvin_exercise_not_found'))
            );
        }
    }

    /**
     * process exercise form submissions and manipulate data.
     */
    private function showSettings() : void
    {
        $exercise = $this->repository->get(self::TEST_ID);
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
            $this->tpl->setContent(
                $this->renderErrorMessage($this->txt('xvin_exercise_not_found'))
            );
        }
    }
}