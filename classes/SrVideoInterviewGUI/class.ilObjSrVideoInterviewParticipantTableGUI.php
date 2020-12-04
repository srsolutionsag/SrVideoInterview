<?php

use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;

/**
 * Class ilObjSrVideoInterviewParticipantTableGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilObjSrVideoInterviewParticipantTableGUI extends ilTable2GUI
{
    // necessary?
    const TABLE_NAME = 'participant_table';
    /**
     * @var \ILIAS\DI\UIServices
     */
    protected $ui;

    /**
     * Initialise ilObjSrVideoInterviewParticipantTableGUI
     * @param        $a_parent_obj
     * @param string $a_parent_cmd
     * @param array  $data
     * @param string $a_template_context
     */
    public function __construct($a_parent_obj, $a_parent_cmd = "", $data = array(), $a_template_context = "")
    {
        global $DIC;
        $this->ui = $DIC->ui();
        $this->setId('test');
        $this->setPrefix('prefix');

        parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);
        $this->setRowTemplate(
            "tpl.participant_table_row.html",
            "./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview"
        );

        $this->addColumns();
        $this->setData($data);
    }

    /**
     * @inheritDoc
     * @return array
     */
    public function getSelectableColumns() : array
    {
        return array(
            'resource' => array(
                'txt' => 'Thumbnail',
                'default' => true,
                'width' => 'auto',
                'sort_field' => 'thumbnail',
            ),

            'has_answered' => array(
                'txt' => 'Answered',
                'default' => true,
                'width' => 'auto',
                'sort_field' => 'has_answered',
            ),

            'firstname' => array(
                'txt' => 'Firstname',
                'default' => true,
                'width' => 'auto',
                'sort_field' => 'firstname',
            ),

            'lastname' => array(
                'txt' => 'Lastname',
                'default' => true,
                'width' => 'auto',
                'sort_field' => 'lastname',
            ),

            'actions' => array(
                'txt' => 'Actions',
                'default' => true,
                'width' => '50px',
            ),
        );
    }

    /**
     * add all selectable columns to the table.
     */
    protected function addColumns() : void
    {
        foreach ($this->getSelectableColumns() as $k => $v) {
            $this->addColumn($v['txt'], null);
        }
    }

    /**
     * @inheritDoc
     * @param array $data
     */
    protected function fillRow($data) : void
    {
        // echo var_dump($data); exit;

        $this->tpl->setVariable('FIRSTNAME', $data['usr_data_firstname']);
        $this->tpl->setVariable('LASTNAME', $data['usr_data_lastname']);
        $this->tpl->setVariable('LOGIN', $data['usr_data_login']);

        $modal = $this->ui->factory()->modal()->interruptive(
            "Wollen sie löschen",
            '#',
            '#'
        )->withAffectedItems([$this->ui->factory()->modal()->interruptiveItem(122, 'titel des benutzers')]);

        $actions = $this->ui->factory()->dropdown()->standard([
            $this->ui->factory()->button()->shy('blabla', '#'),
            $this->ui->factory()->button()->shy('löschen', '#')->withOnClick($modal->getShowSignal())
        ]);

        $this->tpl->setVariable('ACTIONS', $this->ui->renderer()->render([$actions, $modal]));

    }
}