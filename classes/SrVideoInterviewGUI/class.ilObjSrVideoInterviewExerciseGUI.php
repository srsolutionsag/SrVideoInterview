<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARAnswer;

/**
 * ilObjVideoInterviewExerciseGUI is responsible for managing Exercises for a VideoInterview.
 *
 * since we currently only support 1:1 cardinality (Exercise-VideoInterview) methods of
 * this class are partially implemented in ilObjSrVideoInterviewGUI and ilObjSrVideoInterview.
 *
 * @see ilObjSrVideoInterviewGUI::editVideoInterview()
 * @see ilObjSrVideoInterviewGUI::updateVideoInterview()
 * @see ilObjSrVideoInterview::doDelete()
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjVideoInterviewExerciseGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewExerciseGUI extends ilObjSrVideoInterviewGUI
{
    /**
     * @var string tab-name and translation-var
     */
    const EXERCISE_TAB = 'exercise_tab';

    /**
     * Exercise GUI commands
     */
    const CMD_EXERCISE_INDEX  = 'showAll';
    const CMD_EXERCISE_SHOW   = 'showExercise';
    const CMD_EXERCISE_ADD    = 'addExercise';
    const CMD_EXERCISE_EDIT   = 'editExercise';
    const CMD_EXERCISE_DELETE = 'deleteExercise';

    /**
     * Initialise ilObjVideoInterviewExerciseGUI and load further dependencies.
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
            case self::CMD_EXERCISE_EDIT:
            case self::CMD_EXERCISE_DELETE:
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
     * displays all existing Exercises for current VideoInterview object.
     * currently assumes that only one Exercise for $this->obj_id is retrieved.
     *
     * @TODO: implement exercise-list-view when supporting m:1 cardinality.
     */
    protected function showAll() : void
    {
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

            $this->tpl->setContent($tpl->get());
        } else {
            $this->objectNotFound();
        }
    }

    /**
     * displays an existing Exercise.
     *
     * @TODO: implement this, when supporting m:1 cardinality.
     */
    protected function showExercise() : void
    {

    }

    /**
     * displays an edit-form for an Exercise. Currently calls parent-method, which
     * integrates the form in the VideoRepository Settings page.
     *
     * @see ilObjSrVideoInterviwGUI::editVideoInterview()
     *
     * @TODO: implement parent-method here, when supporting m:1 cardinality.
     */
    protected function editExercise() : void
    {
        $this->editVideoInterview();
    }

    /**
     * deletes an existing Exercise for the current VideoInterview by it's id.
     *
     * @TODO: implement this when supporting m:1 cardinality.
     */
    protected function deleteExercise() : void
    {

    }

    /**
     * delete all existing Exercises for the current VideoInterview.
     * this is currently implemented in ilObjSrVideoInterview, when a VideoInterview is deleted.
     *
     * @see ilObjSrVideoInterview::doDelete()
     *
     * @TODO: implement this when supporting m:1 cardinality.
     */
    protected function deleteAllExercises() : void
    {

    }
}
