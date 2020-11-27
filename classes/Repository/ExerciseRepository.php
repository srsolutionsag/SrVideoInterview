<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARExercise;

/**
 * Class ExerciseRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ExerciseRepository implements Repository
{
    /**
     * @inheritDoc
     */
    public function delete(int $exercise_id) : bool
    {
        $ar_exercise = ARExercise::find($exercise_id);
        if (null !== $ar_exercise) {
            $ar_exercise->delete();
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function store(object $exercise) : bool
    {
        if (!$exercise instanceof Exercise) return false;
        $ar_exercise = ARExercise::find($exercise->getId());
        if (null === $ar_exercise) {
            $ar_exercise = new ARExercise();
            $ar_exercise->setId($exercise->getId());
        }

        $ar_exercise
            ->setTitle($exercise->getTitle())
            ->setDescription($exercise->getDescription())
            ->setDetailedDescription($exercise->getDetailedDescription())
            ->setResourceId($exercise->getResourceId())
            ->setObjId($exercise->getObjId())
            ->store()
        ;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(int $exercise_id) : ?Exercise
    {
        $ar_exercise = ARExercise::find($exercise_id);
        if (null !== $ar_exercise) {
            return new Exercise(
                $ar_exercise->getId(),
                $ar_exercise->getTitle(),
                $ar_exercise->getDescription(),
                $ar_exercise->getDetailedDescription(),
                $ar_exercise->getResourceId(),
                $ar_exercise->getObjId()
            );
        }

        return null;
    }

    /**
     * retrieve all exercises for a repository obj by it's id.
     *
     * @param int $obj_id
     * @return Exercise|null
     */
    public function getByObjId(int $obj_id) : ?array
    {
        $ar_exercises = ARExercise::where([
            'obj_id' => $obj_id
        ], "=");

        $exercises = [];
        if (!empty($ar_exercises)) {
            foreach ($ar_exercises as $exercise) {
                array_push($exercises, new Exercise(
                    $exercise->getId(),
                    $exercise->getTitle(),
                    $exercise->getDescription(),
                    $exercise->getDetailedDescription(),
                    $exercise->getResourceId(),
                    $exercise->getObjId()
                ));
            }

            return $exercises;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAll() : ?array
    {
        $ar_exercises = ARExercise::get();
        $exercises = [];

        if (!empty($ar_exercises)) {
            foreach ($ar_exercises as $ar_exercise) {
                array_push($exercises, new Exercise(
                    $ar_exercise->getId(),
                    $ar_exercise->getTitle(),
                    $ar_exercise->getDescription(),
                    $ar_exercise->getDetailedDescription(),
                    $ar_exercise->getResourceId(),
                    $ar_exercise->getObjId()
                ));
            }

            return $exercises;
        }

        return null;
    }
}