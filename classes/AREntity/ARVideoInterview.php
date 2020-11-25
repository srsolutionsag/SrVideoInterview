<?php

namespace srag\Plugins\SrVideoInterview\AREntity;

use ActiveRecord;

/**
 * Class ARVideoInterview
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ARVideoInterview extends ActiveRecord
{
    const TABLE_NAME = 'xvin_interview';

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_primary  true
     * @con_sequence    true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $id = null;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   text
     * @con_length      250
     */
    protected $title = null;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_is_notnull  false
     * @con_fieldtype   clob
     * @con_length      1000
     */
    protected $description = null;

    /**
     * @return string
     */
    public static function getDbTableName() : string
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ARVideoInterview
     */
    public function setId(int $id) : ARVideoInterview
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ARVideoInterview
     */
    public function setTitle(string $title) : ARVideoInterview
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ARVideoInterview
     */
    public function setDescription(string $description) : ARVideoInterview
    {
        $this->description = $description;
        return $this;
    }
}