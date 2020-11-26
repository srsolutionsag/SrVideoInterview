<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview\Entity;

/**
 * Class VideoInterview
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class VideoInterview
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $exercises;

    /**
     * VideoInterview constructor.
     *
     * @param int|null   $id
     * @param string     $title
     * @param string     $description
     * @param array|null $exercises
     */
    public function __construct(int $id = null, string $title = "", string $description = "", array $exercises = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->exercises = $exercises;
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
     * @return VideoInterview
     */
    public function setId(int $id) : VideoInterview
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
     * @return VideoInterview
     */
    public function setTitle(string $title) : VideoInterview
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
     * @return VideoInterview
     */
    public function setDescription(string $description) : VideoInterview
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getExercises() : array
    {
        if (null === $this->exercises) {
            return [];
        }

        return $this->exercises;
    }

    /**
     * @param array $exercises
     * @return VideoInterview
     */
    public function setExercises(array $exercises) : VideoInterview
    {
        $this->exercises = $exercises;
        return $this;
    }
}