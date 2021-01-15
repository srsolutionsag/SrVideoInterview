<?php

use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;

/**
 * Class ilObjSrVideoInterviewParticipantTableGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilObjSrVideoInterviewParticipantTableGUI extends ilTable2GUI
{
    const TABLE_NAME = 'participant_table';

    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;

    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;

    /**
     * @var ilSrVideoInterviewPlugin
     */
    protected $plugin;

    /**
     * @var VideoInterviewRepository
     */
    protected $repository;

    /**
     * Initialise ilObjSrVideoInterviewParticipantTableGUI
     *
     * @param        $a_parent_obj
     * @param string $a_parent_cmd
     * @param string $a_template_context
     */
    public function __construct($a_parent_obj, $a_parent_cmd = "", $a_template_context = "")
    {
        global $DIC;

        $this->plugin      = ilSrVideoInterviewPlugin::getInstance();
        $this->repository  = new VideoInterviewRepository();
        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();

        $this->setId(self::TABLE_NAME);
        $this->setPrefix(self::TABLE_NAME);
        $this->setupTableColumns();
        $this->setRowTemplate(
            "tpl.participant_table_row.html",
            "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview"
        );

        parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);
        $this->gatherTableData();
    }

    /**
     * setup table columns publicly in order for language translation to work easily.
     */
    protected function setupTableColumns() : void
    {
        $this->addColumn($this->plugin->txt('firstname'));
        $this->addColumn($this->plugin->txt('lastname'));
        $this->addColumn($this->plugin->txt('email'));
        $this->addColumn($this->plugin->txt('has_answered'));
        $this->addColumn($this->plugin->txt('invitation_sent'));
        $this->addColumn(
            null,
            null,
            "50px"
        );
    }

    /**
     * gather all participants for the parents repository object id (VideoInterview).
     */
    protected function gatherTableData() : void
    {
        $this->setData($this->repository->getParticipantsByObjId($this->parent_obj->obj_id, true));
    }

    /**
     * @inheritDoc
     * @param array $participant
     */
    protected function fillRow($participant) : void
    {
        $user = new ilObjUser($participant['user_id']);

        // @TODO: implement this passively and m:1 compatible
        $exercise = $this->repository->getExercisesByObjId($this->parent_obj->obj_id)[0];

        $status_light_answered = ($this->repository->hasParticipantAnsweredExercise($participant['id'], $exercise->getId())) ?
            'green' : 'red'
        ;

        $status_light_invited = ($participant['invitation_sent']) ?
            'green' : 'red'
        ;

        $this->tpl->setVariable('FIRSTNAME', $user->getFirstname());
        $this->tpl->setVariable('LASTNAME', $user->getLastname());
        $this->tpl->setVariable('EMAIL', $user->getEmail());

        $this->tpl->setVariable('HAS_ANSWERED', $this->ui_renderer->render(
            $this->ui_factory
                ->legacy("<div class=\"sr-status-light\" style=\"background: {$status_light_answered}\"></div>")
        ));

        $this->tpl->setVariable('INVITATION_SENT', $this->ui_renderer->render(
            $this->ui_factory
                ->legacy("<div class=\"sr-status-light\" style=\"background: {$status_light_invited}\"></div>")
        ));

        $this->ctrl->setParameterByClass(
            ilObjSrVideoInterviewParticipantGUI::class,
            'participant_id',
            $participant['id']
        );

        $actions = array(
            'remove' => $this->ui_factory
                ->button()
                ->shy(
                    $this->plugin->txt('remove_participant'),
                    $this->ctrl->getLinkTargetByClass(
                        ilObjSrVideoInterviewParticipantGUI::class,
                        ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_REMOVE
                    )
                )
            ,

            'notify' => $this->ui_factory
                ->button()
                ->shy(
                    $this->plugin->txt('notify_participant'),
                    $this->ctrl->getLinkTargetByClass(
                        ilObjSrVideoInterviewParticipantGUI::class,
                        ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_NOTIFY
                    )
                )
            ,
        );

        if (null !== ($answer = $this->repository->getParticipantAnswerForExercise($participant['id'], $exercise->getId()))) {
            $this->ctrl->setParameterByClass(
                ilObjSrVideoInterviewAnswerGUI::class,
                'answer_id',
                $answer->getId()
            );

            $actions[] = $this->ui_factory
                ->button()
                ->shy(
                    $this->plugin->txt('show_answer'),
                    $this->ctrl->getLinkTargetByClass(
                        ilObjSrVideoInterviewAnswerGUI::class,
                        ilObjSrVideoInterviewAnswerGUI::CMD_ANSWER_SHOW
                    )
                )
            ;
        }

        $this->tpl->setVariable('ACTIONS', $this->ui_renderer->render(
            $this->ui_factory
                ->dropdown()
                ->standard($actions)
        ));
    }
}
