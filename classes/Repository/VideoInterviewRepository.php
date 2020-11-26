<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\AREntity\ARExercise;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterviewExerciseReference;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\VideoInterview;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterview;

/**
 * Class VideoInterviewRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class VideoInterviewRepository implements Repository
{
    /**
     * @inheritDoc
     */
    public function store(object $obj) : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(int $obj_id) : ?object
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAll() : array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function delete(int $obj_id) : bool
    {
        return true;
    }
}