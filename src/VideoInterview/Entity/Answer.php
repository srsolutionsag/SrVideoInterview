<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview\Entity;

/**
 * Class Answer
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class Answer
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $feedback;

    /**
     * @var string
     */
    protected $resource_id;

    /**
     * @var int
     */
    protected $participant_id;

    /**
     * Answer constructor.
     *
     * @param int|null $id
     * @param string   $feedback
     * @param string   $resource_id
     * @param int|null $participant_id
     */
    public function __construct(int $id = null, int $type = 0, string $feedback = "", string $resource_id = "", int $participant_id = null)
    {
        $this->id = $id;
        $this->feedback = $feedback;
        $this->resource_id = $resource_id;
        $this->participant_id = $participant_id;
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
     * @return Answer
     */
    public function setId(int $id) : Answer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getType() : ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Answer
     */
    public function setType(int $type) : Answer
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
     * @return Answer
     */
    public function setFeedback(string $feedback) : Answer
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
     * @return Answer
     */
    public function setResourceId(string $resource_id) : Answer
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
     * @return Answer
     */
    public function setParticipantId(int $participant_id) : Answer
    {
        $this->participant_id = $participant_id;
        return $this;
    }


}