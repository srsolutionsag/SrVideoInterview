<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\Repository\VideoInterviewRepository;
use srag\Plugins\SrVideoInterview\Repository\VideoInterviewExerciseReferenceRepository;
use srag\Plugins\SrVideoInterview\AREntity\ARExercise;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterviewExerciseReference;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\VideoInterview;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterview;

/**
 * Class ExerciseRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ExerciseRepository implements Repository
{
    /**
     * @var VideoInterviewExerciseReferenceRepository
     */
    protected $reference_repository;

    /**
     * Initialise ExerciseRepository
     */
    public function __construct()
    {
        $this->reference_repository = new VideoInterviewExerciseReferenceRepository();
    }

    /**
     * @inheritDoc
     */
    public function get(int $exercise_id) : ?object
    {
        $ar_exercise = ARExercise::find($exercise_id);
        if (null === $ar_exercise) return null;

        $exercise = new Exercise(
            $exercise_id,
            $ar_exercise->getTitle(),
            $ar_exercise->getDescription(),
            $ar_exercise->getQuestion(),
            $ar_exercise->getResourceId(),
        );

        $exercise->setVideoInterviews(
            $this->reference_repository->getReferencesForEntity($exercise)
        );

        return $exercise;
    }

    /**
     * @inheritDoc
     */
    public function store(object $exercise) : bool
    {
        if (!$exercise instanceof Exercise) return false;

        $ar_exercise = ARExercise::find($exercise->getId());
        if (null !== $ar_exercise) {
            $ar_exercise
                ->setTitle($exercise->getTitle())
                ->setDescription($exercise->getDescription())
                ->setQuestion($exercise->getQuestion())
                ->setResourceId($exercise->getResourceId())
                ->update()
            ;

            $ar_references = $this->reference_repository->getReferencesForEntity($exercise);
            $references = $exercise->getVideoInterviews();
            // create non-existing references and delete existing ones, that are no longer in $references.

        } else {
            $ar_exercise = new ARExercise();
            $ar_exercise
                ->setTitle($exercise->getTitle())
                ->setDescription($exercise->getDescription())
                ->setQuestion($exercise->getQuestion())
                ->setResourceId($exercise->getResourceId())
                ->store()
            ;

            foreach ($exercise->getVideoInterviews() as $interview) {
                $ar_reference = new ARVideoInterviewExerciseReference();
                $ar_reference
                    ->setVideoInterviewId($interview->getId())
                    ->setExerciseId($exercise->getId())
                    ->store()
                ;
            }
        }

        return true;
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