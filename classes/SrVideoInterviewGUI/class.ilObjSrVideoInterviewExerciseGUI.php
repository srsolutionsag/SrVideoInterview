<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARAnswer;

/**
 * Class ilObjVideoInterviewExerciseGUI
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjVideoInterviewExerciseGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewExerciseGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * Exercise GUI tab-names and translation var
     */
    const EXERCISE_TAB        = 'exercise_tab';

    /**
     * Exercise GUI commands
     */
    const CMD_EXERCISE_INDEX  = 'showAll';
    const CMD_EXERCISE_SHOW   = 'showExercise';
    const CMD_EXERCISE_ADD    = 'addExercise';
    const CMD_EXERCISE_EDIT   = 'editExercise';
    const CMD_EXERCISE_DELETE = 'deleteExercise';

    /**
     * Initialise ilObjVideoInterviewExerciseGUI
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
     * dispatches the given command and calls the corresponding method.
     */
    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::EXERCISE_TAB);
        $cmd = $this->ctrl->getCmd(self::CMD_EXERCISE_INDEX);

        switch ($cmd)
        {
            case self::CMD_EXERCISE_INDEX:
            case self::CMD_EXERCISE_SHOW:
                if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
                    $this->$cmd();
                } else {
                    $this->permissionDenied();
                }
                break;
            case self::CMD_EXERCISE_ADD:
            case self::CMD_EXERCISE_EDIT:
            case self::CMD_EXERCISE_DELETE:
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
     * display an existing Exercise and controls depending on user-permission.
     */
    protected function showExercise(int $exercise_id) : void
    {

    }

    /**
     * displays all existing Exercises for current VideoInterview object.
     * currently only supports 1:1 cardinality and just shows one entry.
     */
    protected function showAll() : void
    {
        // assuming 1:1 cardinality.
        $exercise = $this->repository->getExercisesByObjId($this->obj_id)[0];
        if (null !== $exercise) {
            $tpl = new ilTemplate(self::TEMPLATE_DIR . 'tpl.exercise.html', false, false);

            $this->ctrl->setParameterByClass(
                ilObjSrVideoInterviewAnswerGUI::class,
                "exercise_id",
                $exercise->getId()
            );

            $tpl->setVariable('TITLE', $exercise->getTitle());
            $tpl->setVariable('DESCRIPTION_LABEL', $this->txt('description'));
            $tpl->setVariable('DESCRIPTION', $exercise->getDescription());
            $tpl->setVariable('DETAILED_DESCRIPTION_LABEL', $this->txt('exercise_detailed_description'));
            $tpl->setVariable('DETAILED_DESCRIPTION', $exercise->getDetailedDescription());
            $tpl->setVariable('VIDEO', $this->getRecordedVideoHTML($exercise->getResourceId()));
            $tpl->setVariable('ACTION',
                $this->ui_renderer->render(
                    $this->ui_factory->button()->primary(
                        $this->txt('answer'),
                        $this->ctrl->getLinkTargetByClass(
                            ilObjSrVideoInterviewAnswerGUI::class,
                            ilObjSrVideoInterviewAnswerGUI::CMD_ANSWER_SHOW
                        )
                    )
                )
            );

            $this->tpl->setContent(
                $tpl->get()
            );
        } else {
            $this->objectNotFound();
        }
    }

    /**
     * create a Exercise with the same object title and description
     * and store it in the database.
     */
    protected function addExercise() : void
    {
        $exercise = new Exercise(
            null,
            $this->object->getTitle(),
            $this->object->getDescription(),
            "Replace me :).",
            "",
            $this->obj_id
        );

        $this->repository->store($exercise);
        $this->ctrl->redirectByClass(
            self::class,
            self::CMD_EXERCISE_INDEX
        );
    }

    /**
     * edit an existing Exercise on the Repository Object settings-page.
     * implement this when m:1 is required.
     */
    protected function editExercise() : void
    {
        $this->editVideoInterview();
    }

    /**
     * delete one or all existing Exercises when a VideoInterview is deleted.
     */
    protected function deleteExercise() : void
    {
        // @TODO: somehow delete (all) exercise(s) when repository-object is deleted.
    }
}
