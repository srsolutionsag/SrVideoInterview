<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\AREntity\ARAnswer;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Answer;

/**
 * Class AnswerRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class AnswerRepository implements Repository
{
    public function delete(int $answer_id) : bool
    {
        $ar_answer = ARAnswer::find($answer_id);
        if (null !== $ar_answer) {
            $ar_answer->delete();
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function store(object $answer) : bool
    {
        if (!$answer instanceof Answer) return false;
        $ar_answer = ARAnswer::find($answer->getId());
        if (null === $ar_answer) {
            $ar_answer = new ARAnswer();
            $ar_answer->setId($answer->getId());
        }

        $ar_answer
            ->setFeedback($answer->getFeedback())
            ->setResourceId($answer->getResourceId())
            ->setParticipantId($answer->getParticipantId())
            ->store()
        ;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(int $answer_id) : ?Answer
    {
        $ar_answer = ARAnswer::find($answer_id);
        if (null !== $ar_answer) {
            return new Answer(
                $ar_answer->getId(),
                $ar_answer->getFeedback(),
                $ar_answer->getResourceId(),
                $ar_answer->getParticipantId()
            );
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAll() : ?array
    {
        $ar_answers = ARAnswer::get();
        $answers = [];

        if (!empty($ar_answers)) {
            foreach ($ar_answers as $ar_answer) {
                array_push($answers, new Answer(
                    $ar_answer->getId(),
                    $ar_answer->getFeedback(),
                    $ar_answer->getResourceId(),
                    $ar_answer->getParticipantId()
                ));
            }

            return $answers;
        }

        return null;
    }
}