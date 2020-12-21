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
    }

    /**
     * setup table columns publicly in order for language translation to work easily.
     */
    public function setupTableColumns() : void
    {
        $this->addColumn($this->plugin->txt('has_answered')); // display as red or green light
        $this->addColumn($this->plugin->txt('firstname'));
        $this->addColumn($this->plugin->txt('lastname'));
        $this->addColumn($this->plugin->txt('email'));
        $this->addColumn(
            null,
            null,
            "50px"
        );
    }

    /**
     * @inheritDoc
     * @param array $data
     */
    protected function fillRow($data) : void
    {
        // for 1:1 cardinality, display green/red lights
        $this->tpl->setVariable(
            'HAS_ANSWERED',
            "not yet implemented."
        );

        $this->tpl->setVariable(
            'FIRSTNAME',
            $data['usr_data_firstname']
        );

        $this->tpl->setVariable(
            'LASTNAME',
            $data['usr_data_lastname']
        );

        $this->tpl->setVariable(
            'EMAIL',
            $data['usr_data_email']
        );

        $this->ctrl->setParameterByClass(
            ilObjSrVideoInterviewParticipantGUI::class,
            'participant_id',
            $data['id']
        );

        $remove_link = $this->ctrl->getLinkTargetByClass(
            ilObjSrVideoInterviewParticipantGUI::class,
            ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_REMOVE
        );

        $respond_link = $this->ctrl->getLinkTargetByClass(
            ilObjSrVideoInterviewParticipantGUI::class,
            ilObjSrVideoInterviewParticipantGUI::CMD_PARTICIPANT_RESPOND
        );

        $show_answer_link = $this->ctrl->getLinkTargetByClass(
            ilObjSrVideoInterviewAnswerGUI::class,
            ilObjSrVideoInterviewAnswerGUI::CMD_ANSWER_SHOW
        );

        $this->tpl->setVariable(
            'ACTIONS',
            $this->ui_renderer->render(
                $this->ui_factory
                    ->dropdown()
                    ->standard(array(
                        $this->ui_factory
                            ->button()
                            ->primary(
                                $this->plugin->txt('respond_to_participant'),
                                $respond_link
                            )
                        ,

                        $this->ui_factory
                            ->button()
                            ->shy(
                                $this->plugin->txt('remove_participant'),
                                $remove_link
                            )
                        ,
                    ))

            )
        );
    }
}
