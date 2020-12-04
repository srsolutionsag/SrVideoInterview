<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview\Entity;

/**
 * Class Participant
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class Participant
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $feedback_sent;

    /**
     * @var bool
     */
    protected $invitation_sent;

    /**
     * @var int
     */
    protected $exercise_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * Participant constructor.
     *
     * @param int|null $id
     * @param bool     $feedback_sent
     * @param bool     $invitation_sent
     * @param int|null $exercise_id
     * @param int|null $user_id
     */
    public function __construct(int $id = null, bool $feedback_sent = false, bool $invitation_sent = false, int $exercise_id = null, int $user_id = null)
    {
        $this->id = $id;
        $this->feedback_sent = $feedback_sent;
        $this->invitation_sent = $invitation_sent;
        $this->exercise_id = $exercise_id;
        $this->user_id = $user_id;
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
     * @return Participant
     */
    public function setId(int $id) : Participant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFeedbackSent() : bool
    {
        return $this->feedback_sent;
    }

    /**
     * @param bool $feedback_sent
     * @return Participant
     */
    public function setFeedbackSent(bool $feedback_sent) : Participant
    {
        $this->feedback_sent = $feedback_sent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInvitationSent() : bool
    {
        return $this->invitation_sent;
    }

    /**
     * @param bool $invitation_sent
     * @return Participant
     */
    public function setInvitationSent(bool $invitation_sent) : Participant
    {
        $this->invitation_sent = $invitation_sent;
        return $this;
    }

    /**
     * @return int
     */
    public function getExerciseId() : int
    {
        return $this->exercise_id;
    }

    /**
     * @param int $exercise_id
     * @return Participant
     */
    public function setExerciseId(int $exercise_id) : Participant
    {
        $this->exercise_id = $exercise_id;
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
     * @return Participant
     */
    public function setUserId(int $user_id) : Participant
    {
        $this->user_id = $user_id;
        return $this;
    }
}