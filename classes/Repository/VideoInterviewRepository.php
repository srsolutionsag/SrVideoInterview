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
     * Initialise VideoInterviewRepository
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
     * retrieve all Participants assigned to a VideoInterview by it's obj_id. retrieve
     * participants as an array instead of Participant objects by enabling $array_data.
     *
     * @param int  $obj_id
     * @param bool $array_data
     * @return array|null
     */
    public function getParticipantsByObjId(int $obj_id, bool $array_data = false) : ?array
    {
        if ($array_data) {
            return $this->participant_repository->getParticipantsArrayDataByObjId($obj_id);
        }

        return $this->participant_repository->getParticipantsByObjId($obj_id);
    }

    /**
     * check if a Participant has already answered an Exercise by their id's.
     *
     * @param int $participant_id
     * @param int $exercise_id
     * @return bool
     */
    public function hasParticipantAnsweredExercise(int $participant_id, int $exercise_id) : bool
    {
        return $this->answer_repository->hasParticipantAnsweredExercise($participant_id, $exercise_id);
    }

    /**
     * retrieve an existing Participant by it's id.
     *
     * @param int $participant_id
     * @return Participant|null
     */
    public function getParticipantById(int $participant_id) : ?Participant
    {
        return $this->participant_repository->get($participant_id);
    }

    /**
     * retrieve a Participant by the assigned user id.
     *
     * @param int $user_id
     * @return Participant|null
     */
    public function getParticipantForObjByUserId(int $obj_id, int $user_id) : ?Participant
    {
        return $this->participant_repository->getParticipantForObjByUserId($obj_id, $user_id);
    }

    /**
     * delete an existing Participant by it's id.
     *
     * @param int $participant_id
     * @return bool
     */
    public function removeParticipantById(int $participant_id) : bool
    {
        return $this->participant_repository->delete($participant_id);
    }

    /**
     * retrieve an existing answer of a participant for an exercise.
     *
     * @param int $participant_id
     * @param int $exercise_id
     * @return Answer|null
     */
    public function getParticipantAnswerForExercise(int $participant_id, int $exercise_id) : ?Answer
    {
        return $this->answer_repository->getParticipantAnswerForExercise($participant_id, $exercise_id);
    }
}