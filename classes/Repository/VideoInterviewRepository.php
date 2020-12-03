<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\Repository\AnswerRepository;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\Repository\ParticipantRepository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Answer;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;

/**
 * Class VidoInterviewRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
final class VideoInterviewRepository
{
    /**
     * @var AnswerRepository
     */
    private $answer_repository;

    /**
     * @var ExerciseRepository
     */
    private $exercise_repository;

    /**
     * @var ParticipantRepository
     */
    private $participant_repository;

    /**
     * Initialise VidoInterviewRepository
     */
    public function __construct()
    {
        $this->answer_repository      = new AnswerRepository();
        $this->exercise_repository    = new ExerciseRepository();
        $this->participant_repository = new ParticipantRepository();
    }

    /**
     * create a new or update an existing Answer, Exercise or Participant object.
     *
     * @param Object $obj
     * @return bool
     */
    public function store(Object $obj) : bool
    {
        switch ($obj)
        {
            case $obj instanceof Answer:
                return $this->answer_repository->store($obj);
            case $obj instanceof Exercise:
                return $this->exercise_repository->store($obj);
            case $obj instanceof Participant:
                return $this->participant_repository->store($obj);
            default:
                return false;
        }
    }

    /**
     * retrieve an existing Exercise by it's id.
     *
     * @param int $exercise_id
     * @return Exercise|null
     */
    public function getExerciseById(int $exercise_id) : ?Exercise
    {
        return $this->exercise_repository->get($exercise_id);
    }

    /**
     * retrieve all existing Exercises for a given VideoInterview (object) id.
     *
     * @param int $obj_id
     * @return array|null
     */
    public function getExercisesByObjId(int $obj_id) : ?array
    {
        return $this->exercise_repository->getByObjId($obj_id);
    }

    /**
     * retrieve all existing Exercises.
     *
     * @return array|null
     */
    public function getAllExercises() : ?array
    {
        return $this->exercise_repository->getAll();
    }

    /**
     * retrieve all participants currently added to an exercise by it's id.
     *
     * @param int $exercise_id
     * @return array|null
     */
    public function getParticipantsByExerciseId(int $exercise_id) : ?array
    {
        return $this->participant_repository->getParticipantByExerciseId($exercise_id);
    }
}