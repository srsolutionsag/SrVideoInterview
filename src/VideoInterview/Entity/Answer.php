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
    protected $content;

    /**
     * @var string
     */
    protected $resource_id;

    /**
     * @var string
     */
    protected $thumbnail_id;

    /**
     * @var int
     */
    protected $exercise_id;

    /**
     * @var int
     */
    protected $participant_id;

    /**
     * Answer constructor.
     *
     * @param int|null $id
     * @param int      $type
     * @param string   $content
     * @param string   $resource_id
     * @param int|null $exercise_id
     * @param int|null $participant_id
     */
    public function __construct(int $id = null, int $type = 0, string $content = "", string $resource_id = "", string $thumbnail_id = "", int $exercise_id = null, int $participant_id = null)
    {
        $this->id = $id;
        $this->content = $content;
        $this->type = $type;
        $this->resource_id = $resource_id;
        $this->thumbnail_id = $thumbnail_id;
        $this->exercise_id = $exercise_id;
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
    public function getContent() : ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Answer
     */
    public function setContent(?string $content) : Answer
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId() : ?string
    {
        return $this->resource_id;
    }

    /**
     * @param string|null $resource_id
     * @return Answer
     */
    public function setResourceId(?string $resource_id) : Answer
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnailId() : ?string
    {
        return $this->thumbnail_id;
    }

    /**
     * @param string|null $thumbnail_id
     * @return Answer
     */
    public function setThumbnailId(?string $thumbnail_id) : Answer
    {
        $this->thumbnail_id = $thumbnail_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getExerciseId() : ?int
    {
        return $this->exercise_id;
    }

    /**
     * @param int $exercise_id
     * @return Answer
     */
    public function setExerciseId(int $exercise_id) : Answer
    {
        $this->exercise_id = $exercise_id;
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