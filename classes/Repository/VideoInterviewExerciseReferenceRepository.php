<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\ReferenceRepository;
use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;
use srag\Plugins\SrVideoInterview\AREntity\ARExercise;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterviewExerciseReference;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\VideoInterview;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterview;
use function Sabre\Event\Loop\instance;

/**
 * Class VideoInterviewExerciseReferenceRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class VideoInterviewExerciseReferenceRepository implements ReferenceRepository
{
    private function determineEntityIdField(object $entity) : ?string
    {
        if ($entity instanceof Exercise) {
            return 'exercise_id';
        } else if ($entity instanceof VideoInterview) {
            return 'video_interview_id';
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getReferencesForEntity(object $entity) : ?array
    {
        $id_field = $this->determineEntityIdField($entity);
        if (null === $id_field) return null;

        $result = ARVideoInterviewExerciseReference::where([
            $id_field => $entity->getId(),
        ], "=")->getArray();

        return (empty($result)) ? null: $result;
    }

    /**
     * @inheritDoc
     */
    public function store(int $interview_id, int $exercise_id) : bool
    {
        $ar_reference = ARVideoInterviewExerciseReference::where([
            'video_interview_id' => $interview_id,
            'exercise_id'        => $exercise_id,
        ], "=")->getArray();

        if (empty($ar_reference)) {
            $ar_reference[0] = new ARVideoInterviewExerciseReference();
            $ar_reference[0]->create();
        }

        $ar_reference[0]
            ->setVideoInterviewId($interview_id)
            ->setExerciseId($exercise_id)
            ->update()
        ;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteReferencesFromEntity(object $entity) : bool
    {
        $id_field = $this->determineEntityIdField($entity);
        if (null === $id_field) return false;

        $ar_references = ARVideoInterviewExerciseReference::where([
            $id_field => $entity->getId(),
        ], "=")->getArray();

        if (!empty($ar_references)) {
            foreach ($ar_references as $reference) {
                $reference->delete();
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteReferenceById(int $ref_id) : bool
    {
        $ar_reference = ARVideoInterviewExerciseReference::find($ref_id);
        if (null === $ar_reference) return false;

        $ar_reference->delete();

        return true;
    }
}