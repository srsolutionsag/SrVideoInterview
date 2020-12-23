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
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $detailed_description;

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
    protected $obj_id;

    /**
     * Exercise constructor.
     * @param int|null $id
     * @param string   $title
     * @param string   $description
     * @param string   $detailed_description
     * @param string   $resource_id
     * @param string   $thumbnail_id
     * @param int|null $obj_id
     */
    public function __construct(int $id = null, string $title = "", string $description = "", $detailed_description = "", string $resource_id = "", string $thumbnail_id = "", int $obj_id = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->detailed_description = $detailed_description;
        $this->resource_id = $resource_id;
        $this->thumbnail_id = $thumbnail_id;
        $this->obj_id = $obj_id;
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
    public function getDetailedDescription() : string
    {
        return $this->detailed_description;
    }

    /**
     * @param string $detailed_description
     * @return Exercise
     */
    public function setDetailedDescription(string $detailed_description) : Exercise
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
     * @return Exercise
     */
    public function setResourceId(string $resource_id) : Exercise
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
     * @return Exercise
     */
    public function setThumbnailId(?string $thumbnail_id) : Exercise
    {
        $this->thumbnail_id = $thumbnail_id;
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
     * @return Exercise
     */
    public function setObjId(int $obj_id) : Exercise
    {
        $this->obj_id = $obj_id;
        return $this;
    }
}