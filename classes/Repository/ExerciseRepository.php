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
     * build an Exercise from array.
     *
     * @param array $args
     * @return Exercise
     */
    protected function buildExercise(array $args = array(
        'id' => null,
        'title' => "",
        'description' => "",
        'detailed_description' => "",
        'resource_id' => "",
        'thumbnail_id' => "",
        'obj_id' => null
    )) : Exercise
    {
        return new Exercise(
            $args['id'],
            $args['title'],
            $args['description'],
            $args['detailed_description'],
            $args['resource_id'],
            $args['thumbnail_id'],
            $args['obj_id']
        );
    }

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
            ->setThumbnailId($exercise->getThumbnailId())
            ->setObjId($exercise->getObjId())
            ->store()
        ;

        return true;
    }

    /**
     * @@return Exercise
     */
    public function get(int $exercise_id) : ?object
    {
        $ar_exercise = ARExercise::find($exercise_id);
        if (null !== $ar_exercise) {
            return new Exercise(
                $ar_exercise->getId(),
                $ar_exercise->getTitle(),
                $ar_exercise->getDescription(),
                $ar_exercise->getDetailedDescription(),
                $ar_exercise->getResourceId(),
                $ar_exercise->getThumbnailId(),
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
        ], "=")->getArray();

        $exercises = [];
        if (null !== $ar_exercises) {
            foreach ($ar_exercises as $exercise) {
                $exercises[] = $this->buildExercise($exercise);
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
                $exercises[] = new Exercise(
                    $ar_exercise->getId(),
                    $ar_exercise->getTitle(),
                    $ar_exercise->getDescription(),
                    $ar_exercise->getDetailedDescription(),
                    $ar_exercise->getResourceId(),
                    $ar_exercise->getThumbnailId(),
                    $ar_exercise->getObjId()
                );
            }

            return $exercises;
        }

        return null;
    }
}
