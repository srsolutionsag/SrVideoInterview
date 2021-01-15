<?php

require_once __DIR__ . "/class.ilObjSrVideoInterviewGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewExerciseGUI.php";
require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewParticipantGUI.php";

use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;

/**
 * Class ilObjSrVideoInterviewListGUI
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewListGUI extends ilObjectPluginListGUI
{
    /**
     * @var VideoInterviewRepository
     */
    protected $repository;

    /**
     * ilObjSrVideoInterviewListGUI constructor.
     *
     * @param int $a_context
     */
    public function __construct($a_context = self::CONTEXT_REPOSITORY)
    {
        global $DIC;

        $this->repository = new VideoInterviewRepository();
        $DIC->ui()->mainTemplate()->addCss(
            ilObjSrVideoInterviewGUI::CSS_DIR . "style.general.css"
        );

        parent::__construct($a_context);
    }

    /**
     * @inheritDoc
     */
    public function initType() : void
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getGuiClass() : string
    {
        return ilObjSrVideoInterviewGUI::class;
    }

    /**
     * @TODO: drop this method once m:1 cardinality is supported.
     *
     * @ineritdoc
     * @return array
     */
    public function getProperties() : array
    {
        $parent = parent::getProperties();

        if (!$this->access->checkAccess("write", "", $this->ref_id)) {
            $current_participant = $this->repository->getParticipantForObjByUserId($this->obj_id, $this->user->getId());
            if (null !== $current_participant) {
                // assuming 1:1 cardinality
                $exercise = $this->repository->getExercisesByObjId($this->obj_id)[0];
                $status_light = ($this->repository->hasParticipantAnsweredExercise($current_participant->getId(), $exercise->getId())) ?
                    'green' :
                    'red'
                ;

                $parent[] = array(
                    'property' => $this->txt('status'),
                    'value'    => "
                        <div class=\"sr-list-view-item\">
                            <p class=\"sr-status-light\" style=\"background-color: {$status_light};\"></p>
                        </div>"
                );
            }
        }

        return $parent;
    }

    /**
     * initialises the base-commands of a VideoInterview.
     * @return array
     */
    public function initCommands()
    {
        $this->copy_enabled = false;
        $this->enableTags(true);

        return array(
            array(
                'permission' => 'read',
                'cmd'        => ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_INDEX,
                'default'    => true
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_INDEX,
                'default'    => false
            ),
            array(
                'permission' => 'write',
                'cmd'        => ilObjSrVideoInterviewExerciseGUI::CMD_EXERCISE_EDIT,
                'txt'        => $this->txt('edit'),
                'default'    => false
            ),
        );
    }
}
