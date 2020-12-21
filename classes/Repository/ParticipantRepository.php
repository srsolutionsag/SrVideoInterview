<?php

namespace srag\Plugins\SrVideoInterview\Repository;

use srag\Plugins\SrVideoInterview\AREntity\ARParticipant;
use srag\Plugins\SrVideoInterview\VideoInterview\Entity\Participant;
use srag\Plugins\SrVideoInterview\VideoInterview\Repository;

/**
 * Class ParticipantRepository
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ParticipantRepository implements Repository
{
    /**
     * transform ARParticipant array to Participant array
     * @param array $ar_participants
     * @return array|null
     */
    protected function transformToParticipant(array $ar_participants) : ?array
    {
        $participants = [];
        if (!empty($ar_participants)) {
            foreach ($ar_participants as $ar_participant) {
                $participants[] = new Participant(
                    $ar_participant['id'],
                    (bool) $ar_participant['invitation_sent'],
                    $ar_participant['obj_id'],
                    $ar_participant['user_id']
                );
            }

            return $participants;
        }

        return null;
    }

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
        if (!$participant instanceof Participant) {
            return false;
        }
        $ar_participant = ARParticipant::where(['user_id' => $participant->getUserId(), 'obj_id' => $participant->getObjId()])->first();
        if (!$ar_participant instanceof ARParticipant) {
            $ar_participant = ARParticipant::find($participant->getId());
            if (null === $ar_participant || $participant->getId() === null) {
                $ar_participant = new ARParticipant();
                $ar_participant->setObjId($participant->getObjId());
                $ar_participant->setUserId($participant->getUserId());
                $ar_participant->create();
            }
        }

        $ar_participant
            ->setInvitationSent($participant->isInvitationSent())
            ->setObjId($participant->getObjId())
            ->setUserId($participant->getUserId())
            ->update();

        return true;
    }

    /**
     * @return Participant
     */
    public function get(int $participant_id) : ?object
    {
        $ar_participant = ARParticipant::find($participant_id);

        if (null !== $ar_participant) {
            return new Participant(
                $ar_participant->getId(),
                $ar_participant->isInvitationSent(),
                $ar_participant->getObjId(),
                $ar_participant->getUserId()
            );
        }

        return null;
    }

    /**
     * @TODO: unnecessary user_data provided.
     *
     * @inheritDoc
     */
    public function getAll() : ?array
    {
        $ar_participants = ARParticipant::innerjoin(
            "usr_data",
            "user_id",
            "usr_id",
            array(
                'firstname',
                'lastname',
                'email'
            )
        )->getArray();

        return $this->transformToParticipant($ar_participants);
    }

    /**
     * retrieve a Participant for a VideoInterview by the assigned user id.
     *
     * @param int $obj_id
     * @param int $user_id
     * @return Participant|null
     */
    public function getParticipantForObjByUserId(int $obj_id, int $user_id) : ?Participant
    {
        $ar_participant = ARParticipant::where(array(
            'obj_id' => $obj_id,
            'user_id' => $user_id,
        ), "=")->first();

        if (null !== $ar_participant) {
            return new Participant(
                $ar_participant->getId(),
                $ar_participant->isInvitationSent(),
                $ar_participant->getObjId(),
                $ar_participant->getUserId()
            );
        }

        return null;
    }

    /**
     * retrieve all Participants assigned to a VideoInterview by it's obj_id.
     *
     * @param int $obj_id
     * @return Participant[]|null
     */
    public function getParticipantsByObjId(int $obj_id) : ?array
    {
        $ar_participants = ARParticipant::where(array(
            'obj_id' => $obj_id,
        ),'=')->get();

        if (null !== $ar_participants) {
            $participants = array();
            foreach ($ar_participants as $participant) {
                $participants[] = new Participant(
                    $participant->getId(),
                    $participant->isInvitationSent(),
                    $participant->getObjId(),
                    $participant->getUserId()
                );
            }

            return $participants;
        }

        return null;
    }

    /**
     * retrieve all Participants assigned to a VideoInterview by it's obj_id as an array.
     *
     * @param int $obj_id
     * @return array|null
     */
    public function getParticipantsArrayDataByObjId(int $obj_id) : ?array
    {
        return ARParticipant::where(array(
            'obj_id' => $obj_id,
        ), '=')->getArray();
    }
}
