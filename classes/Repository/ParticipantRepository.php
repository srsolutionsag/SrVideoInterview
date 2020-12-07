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
            ->setObjId($participant->getObjId())
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
                $ar_participant->getObjId(),
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
                $participants[] = new Participant(
                    $ar_participant->getId(),
                    (bool) $ar_participant->getFeedbackSent(),
                    (bool) $ar_participant->getInvitationSent(),
                    $ar_participant->getObjId(),
                    $ar_participant->getUserId()
                );
            }

            return $participants;
        }

        return null;
    }

    /**
     * retrieve all participants currently added to an exercise by it's id.
     * @param int $obj_id
     * @return array|null
     */
    public function getParticipantByExerciseId(int $obj_id) : ?array
    {
        $ar_participants = ARParticipant::innerjoin(
            'usr_data',
            "user_id",
            "usr_id",
            array(
                'firstname',
                'lastname',
                'login',
            )
        )->where([
            'obj_id' => $obj_id,
        ],
            '='
        )->getArray();



        return $ar_participants ?? null;
    }
}