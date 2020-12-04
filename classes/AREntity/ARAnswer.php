<?php

namespace srag\Plugins\SrVideoInterview\AREntity;

use ActiveRecord;

/**
 * Class ARAnswer
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ARAnswer extends ActiveRecord
{
    const TABLE_NAME = 'xvin_answer';

    const TYPE_FEEDBACK = 1;
    const TYPE_ANSWER   = 0;

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
     * @var int
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   int
     * @con_length      1
     */
    protected $type;

    /**
     * @var string
     *
     * @TODO: may refactor to $content or $reply or similar.
     *
     * @con_has_field   true
     * @con_is_notnull  false
     * @con_fieldtype   clob
     * @con_length      4000
     */
    protected $feedback = null;

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
    protected $participant_id = null;

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
     * @return ARAnswer
     */
    public function setId(int $id) : ARAnswer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return ARAnswer
     */
    public function setType(int $type) : ARAnswer
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getFeedback() : string
    {
        return $this->feedback;
    }

    /**
     * @param string $feedback
     * @return ARAnswer
     */
    public function setFeedback(string $feedback) : ARAnswer
    {
        $this->feedback = $feedback;
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
     * @return ARAnswer
     */
    public function setResourceId(string $resource_id) : ARAnswer
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getParticipantId() : int
    {
        return $this->participant_id;
    }

    /**
     * @param int $participant_id
     * @return ARAnswer
     */
    public function setParticipantId(int $participant_id) : ARAnswer
    {
        $this->participant_id = $participant_id;
        return $this;
    }
}