<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;

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
     * @var ExerciseRepository
     */
    protected $repository;

    /**
     * Initialise ilObjVideoInterviewExerciseGUI
     *
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
                // we should not reach this.
                break;
        }
    }

    protected function showAll() : void
    {

    }

    protected function showExercise() : void
    {

    }

    protected function addExercise() : void
    {

    }

    protected function editExercise() : void
    {

    }

    protected function deleteExercise() : void
    {

    }
}