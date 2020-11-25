<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\AREntity\ARExercise;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Exercise;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterviewExerciseReference;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\VideoInterview;
use srag\Plugins\SrVideoInterview\AREntity\ARVideoInterview;

class ExerciseRepository implements Repository
{
    /**
     * @inheritDoc
     */
    public function get(int $obj_id) : ?object
    {
        $ar_obj = ARExercise::find($obj_id);
        if (null === $ar_obj) return null;

        $obj = new Exercise(
            $obj_id,
            $ar_obj->getTitle(),
            $ar_obj->getDescription(),
            $ar_obj->getQuestion(),
            $ar_obj->getResourceId(),
            $this->getVideointerviewReferences($obj_id)
        );

        return $obj;
    }

    /**
     * retrieve all interviews referenced with given exercise id.
     *
     * @param int $obj_id
     * @return array
     */
    private function getVideoInterviewReferences(int $obj_id) : array
    {
        $ar_references = ARVideoInterviewExerciseReference::where(['video_interview_id' => $obj_id])->getArray();
        $video_interview_objs = [];
        foreach ($ar_references as $ref)
        {
            $ar_video_interview = ARVideoInterview::find($ref->getVideoInterviewId());
            array_push($video_interview_objs, new VideoInterview(
                $ar_video_interview->getId(),
                $ar_video_interview->getTitle(),
                $ar_video_interview->getDescription()
            ));
        }

        return $video_interview_objs;
    }

    /**
     * @inheritDoc
     */
    public function store(object $obj) : bool
    {
        if (!$obj instanceof Exercise) return false;

        $ar_exercise = ARExercise::find($obj->getId());
        if (null !== $ar_exercise) {
            $ar_exercise
                ->setTitle($obj->getTitle())
                ->setDescription($obj->getDescription())
                ->setQuestion($obj->getQuestion())
                ->setResourceId($obj->getResourceId())
            ;

            $ar_exercise->update();

            // alle exercise_refs die das Objekt hat hinzufügen, alle die das Objekt nicht hat löschen.

//            $ar_exercise_refs = ARVideoInterviewExerciseReference::where([
//                'exercise_id' => $obj->getId()
//            ])->getArray();
//
//            foreach ($obj->getVideoInterviews() as $interview) {
//                $ar_exercise_ref = ARVideoInterviewExerciseReference::where([
//                    'exercise_id' => $obj->getId(),
//                    'video_interview_id' => $interview->getId()
//                ], "=");
//
//                if (null === $ar_exercise_ref) {
//                    $ar_exercise_ref->create();
//                }
//            }
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
}