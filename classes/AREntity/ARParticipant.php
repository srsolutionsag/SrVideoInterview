<?php

namespace srag\Plugins\SrVideoInterview\AREntity;

use ActiveRecord;

/**
 * Class ARAnswer
 *
 * @TODO: remove $feedback_sent, since this is a logical check
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ARParticipant extends ActiveRecord
{
    const TABLE_NAME = 'xvin_participant';

    /**
     * @var int
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
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      1
     */
    protected $feedback_sent = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      1
     */
    protected $invitation_sent = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $obj_id = null;

    /**
     * @var int
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $user_id = null;

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
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ARParticipant
     */
    public function setId(int $id) : ARParticipant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFeedbackSent() : int
    {
        return $this->feedback_sent;
    }

    /**
     * @param int $feedback_sent
     * @return ARParticipant
     */
    public function setFeedbackSent(int $feedback_sent) : ARParticipant
    {
        $this->feedback_sent = $feedback_sent;
        return $this;
    }

    /**
     * @return int
     */
    public function getInvitationSent() : int
    {
        return $this->invitation_sent;
    }

    /**
     * @param int $invitation_sent
     * @return ARParticipant
     */
    public function setInvitationSent(int $invitation_sent) : ARParticipant
    {
        $this->invitation_sent = $invitation_sent;
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
     * @return ARParticipant
     */
    public function setObjId(int $obj_id) : ARParticipant
    {
        $this->obj_id = $obj_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return ARParticipant
     */
    public function setUserId(int $user_id) : ARParticipant
    {
        $this->user_id = $user_id;
        return $this;
    }
}
