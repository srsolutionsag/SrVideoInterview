<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\VideoInterview\Repository;
use srag\Plugins\SrVideoInterview\AREntity\ARParticipant;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;

/**
 * Class ParticipantRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ParticipantRepository implements Repository
{
    /**
     * @inheritDoc
     */
    public function delete(int $participant_id) : bool
    {
        $ar_participant = ARParticipant::find($participant_id);
        if (null !== $ar_participant) {
            $ar_participant->delete();
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function store(object $participant) : bool
    {
        if (!$participant instanceof Participant) return false;
        $ar_participant = ARParticipant::find($participant->getId());
        if (null === $ar_participant) {
            $ar_participant = new ARParticipant();
            $ar_participant->setId($participant->getId());
        }

        $ar_participant
            ->setFeedbackSent((int) $participant->isFeedbackSent())
            ->setInvitationSent((int) $participant->isInvitationSent())
            ->setExerciseId($participant->getExerciseId())
            ->setUserId($participant->getUserId())
            ->store()
        ;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(int $participant_id) : ?Participant
    {
        $ar_participant = ARParticipant::find($participant_id);
        if (null !== $ar_participant) {
            return new Participant(
                $ar_participant->getId(),
                (bool) $ar_participant->getFeedbackSent(),
                (bool) $ar_participant->getInvitationSent(),
                $ar_participant->getExerciseId(),
                $ar_participant->getUserId()
            );
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAll() : ?array
    {
        $ar_participants = ARParticipant::get();
        $participants = [];

        if (!empty($ar_participants)) {
            foreach ($ar_participants as $ar_participant) {
                array_push($participants, new Participant(
                    $ar_participant->getId(),
                    (bool) $ar_participant->getFeedbackSent(),
                    (bool) $ar_participant->getInvitationSent(),
                    $ar_participant->getExerciseId(),
                    $ar_participant->getUserId()
                ));
            }

            return $participants;
        }

        return null;
    }
}