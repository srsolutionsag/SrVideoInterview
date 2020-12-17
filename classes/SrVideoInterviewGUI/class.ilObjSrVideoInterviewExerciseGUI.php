<?php

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/classes/class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;

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
        $exercises = $this->repository->getExercisesByObjId($this->obj_id);
        if (null !== $exercises) {
            $items = array();
            foreach ($exercises as $exercise) {
                $this->ctrl->setParameterByClass(
                    self::class,
                    'exercise_id',
                    $exercise->getId()
                );

                $actions = array(
                    $this->ui_factory
                        ->button()
                        ->shy(
                            $this->txt('answer'),
                            $this->ctrl->getLinkTargetByClass(
                                ilObjSrVideoInterviewAnswerGUI::class,
                                ilObjSrVideoInterviewAnswerGUI::CMD_ANSWER_ADD
                            )
                        )
                );

//                not necessary until m:1 is implemented.
//                if ($this->access->checkAccess("write", self::CMD_EXERCISE_INDEX, $this->ref_id)) {
//                    $actions[] = $this->ui_factory
//                        ->button()
//                        ->shy(
//                            $this->txt('edit'),
//                            $this->ctrl->getLinkTargetByClass(
//                                ilObjSrVideoInterviewGUI::class,
//                                ilObjSrVideoInterviewGUI::CMD_VIDEO_INTERVIEW_EDIT,
//                            )
//                        )
//                    ;
//                }

//                $resource = $this->storage->inline($exercise->getResourceId());
//                echo var_dump($resource);
//                exit;

                $items[] = $this->ui_factory
                    ->item()
                    ->standard($exercise->getTitle())
                    ->withDescription($exercise->getDetailedDescription())
                    ->withProperties(array(
                        $this->txt('description') => $exercise->getDescription(),
//                        $this->txt('exercise_resource') => $this->ui_factory->legacy("
//                            <video controls playsinline>
//                                <source src=\"\" />
//                            </video>
//                        "),
                    ))
                    ->withActions(
                        $this->ui_factory
                            ->dropdown()
                            ->standard(array(
                                $actions
                            ))
                    )
//                    ->withLeadImage(
//                        $this->ui_factory
//                            ->image()
//                            ->responsive(
//                                "/Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/images/exercise_symbol.svg",
//                                ""
//                            )
//
//                    )
                ;
            }

            $list = $this->ui_factory
                ->panel()
                ->listing()
                ->standard(
                    $this->txt('exercises'), array(
                    $this->ui_factory
                        ->item()
                        ->group(
                            "",
                            $items
                        )
                    )
                )
            ;

            $this->tpl->setContent(
                $this->ui_renderer->render($list)
            );
        } else {
            $this->objectNotFound();
        }
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
