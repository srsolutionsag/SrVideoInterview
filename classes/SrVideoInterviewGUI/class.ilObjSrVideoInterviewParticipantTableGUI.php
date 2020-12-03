<?php

use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;

/**
 * Class ilObjSrVideoInterviewParticipantTableGUI
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilObjSrVideoInterviewParticipantTableGUI extends ilTable2GUI
{
    /**
     * @var string table id
     */
    const TABLE_NAME = 'participant_table';

    /**
     * Initialise ilObjSrVideoInterviewParticipantTableGUI
     *
     * @param        $a_parent_obj
     * @param string $a_parent_cmd
     * @param string $a_template_context
     */
    public function __construct($a_parent_obj, $a_parent_cmd = "", $data = array(), $a_template_context = "")
    {
        parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);

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
            'thumbnail' => array(
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
            if ($this->isColumnSelected($k)) {
                if ($v['sort_field']) {
                    $sort = $v['sort_field'];
                } else {
                    $sort = $k;
                }
                $this->addColumn($v['txt'], $sort, $v['width']);
            }
        }
    }

    /**
     * @inheritDoc
     * @param array $data
     */
    protected function fillRow($data) : void
    {

    }
}