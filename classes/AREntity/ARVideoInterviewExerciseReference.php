<?php

namespace srag\Plugins\SrVideoInterview\AREntity;

use ActiveRecord;

/**
 * Class ARVideoInterviewExercise
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ARVideoInterviewExerciseReference extends ActiveRecord
{
    const TABLE_NAME = 'xvin_exercise_ref';

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_primary  true
     * @con_sequence    true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $id = null;

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $video_interview_id = null;

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_is_notnull  true
     * @con_fieldtype   integer
     * @con_length      8
     */
    protected $exercise_id = null;

    /**
     * @return string
     */
    public static function getDbTableName() : string
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
     * @return ARVideoInterviewExerciseReference
     */
    public function setId(int $id) : ARVideoInterviewExerciseReference
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getVideoInterviewId() : int
    {
        return $this->video_interview_id;
    }

    /**
     * @param int $video_interview_id
     * @return ARVideoInterviewExerciseReference
     */
    public function setVideoInterviewId(int $video_interview_id) : ARVideoInterviewExerciseReference
    {
        $this->video_interview_id = $video_interview_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getExerciseId() : int
    {
        return $this->exercise_id;
    }

    /**
     * @param int $exercise_id
     * @return ARVideoInterviewExerciseReference
     */
    public function setExerciseId(int $exercise_id) : ARVideoInterviewExerciseReference
    {
        $this->exercise_id = $exercise_id;
        return $this;
    }
}