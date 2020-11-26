<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview\Entity;

/**
 * Class Exercise
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class Exercise
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
     * @var string
     */
    private $question;

    /**
     * @var string
     */
    private $resource_id;

    /**
     * @var array
     */
    private $videointerviews;

    /**
     * Exercise constructor.
     *
     * @param int|null   $id
     * @param string     $title
     * @param string     $description
     * @param string     $question
     * @param string     $resource_id
     * @param array|null $videointerviews
     */
    public function __construct(int $id = null, string $title = "", string $description = "", $question = "", string $resource_id = "", array $videointerviews = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->question = $question;
        $this->resource_id = $resource_id;
        $this->videointerviews = $videointerviews;
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
     * @return Exercise
     */
    public function setId(int $id) : Exercise
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
     * @return Exercise
     */
    public function setTitle(string $title) : Exercise
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
     * @return Exercise
     */
    public function setDescription(string $description) : Exercise
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion() : string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return Exercise
     */
    public function setQuestion(string $question) : Exercise
    {
        $this->question = $question;
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
     * @return Exercise
     */
    public function setResourceId(string $resource_id) : Exercise
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return array
     */
    public function getVideoInterviews() : array
    {
        if (null === $this->videointerviews) {
            return [];
        }

        return $this->videointerviews;
    }

    /**
     * @param array $videointerviews
     * @return Exercise
     */
    public function setVideoInterviews(array $videointerviews) : Exercise
    {
        $this->videointerviews = $videointerviews;
        return $this;
    }
}