<?php

namespace srag\Plugins\SrVideoInterview\AREntity;

use ActiveRecord;

/**
 * Class ARExercise
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ARExercise extends ActiveRecord
{
    const TABLE_NAME = 'xvin_exercise';

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_primary  true
     * @con_sequence    true
     * @con_is_notnull  true
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
     * @var string
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   clob
     * @con_length      4000
     */
    protected $detailed_description = null;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_is_notnull  false
     * @con_fieldtype   text
     * @con_length      250
     */
    protected $resource_id = null;

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $obj_id = null;

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
     * @return ARExercise
     */
    public function setId(int $id) : ARExercise
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
     * @return ARExercise
     */
    public function setTitle(string $title) : ARExercise
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
     * @return ARExercise
     */
    public function setDescription(string $description) : ARExercise
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDetailedDescription() : string
    {
        return $this->detailed_description;
    }

    /**
     * @param string $detailed_description
     * @return ARExercise
     */
    public function setDetailedDescription(string $detailed_description) : ARExercise
    {
        $this->detailed_description = $detailed_description;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId() : string
    {
        return $this->resource_id;
    }

    /**
     * @param string $resource_id
     * @return ARExercise
     */
    public function setResourceId(string $resource_id) : ARExercise
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }

    /**
     * @param int $obj_id
     * @return ARExercise
     */
    public function setObjId(int $obj_id) : ARExercise
    {
        $this->obj_id = $obj_id;
        return $this;
    }
}