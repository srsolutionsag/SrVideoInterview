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
                    (bool) $ar_participant['feedback_sent'],
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
            ->setFeedbackSent((int) $participant->isFeedbackSent())
            ->setInvitationSent((int) $participant->isInvitationSent())
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
        $ar_participant = ARParticipant::innerjoin(
            "usr_data",
            "user_id",
            "usr_id",
            array(
                'firstname',
                'lastname',
                'email'
            )
        )->where(array(
            'id' => $participant_id
        ),
            "="
        )->getArray();

        if (null !== $ar_participant) {
            return new Participant(
                $ar_participant['id'],
                (bool) $ar_participant['feedback_sent'],
                (bool) $ar_participant['invitation_sent'],
                $ar_participant['obj_id'],
                $ar_participant['user_id']
            );
        }

        return null;
    }

    /**
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
     * retrieve a Participant by the assigned user id.
     * @param int $user_id
     * @return Participant|null
     */
    public function getParticipantByUserId(int $user_id) : ?Participant
    {
        $ar_participant = ARParticipant::where(array(
            'user_id' => $user_id,
        ), "=")->getArray();

        if (!empty($ar_participant)) {
            $arr = array_values($ar_participant);
            return new Participant(
                $arr[0]['id'],
                (bool) $arr[0]['feedback_sent'],
                (bool) $arr[0]['invitation_sent'],
                $arr[0]['obj_id'],
                $arr[0]['user_id']
            );
        }

        return null;
    }

    /**
     * retrieve all participants currently added to an exercise by it's id.
     * @param int $obj_id
     * @return array|null
     */
    public function getParticipantsByObjId(int $obj_id) : ?array
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

        return $ar_participants;
    }

//    /**
//     * retrieve an array of participants where first-, lastname and email contain a given term.
//     *
//     * @param string $term
//     * @return array|null
//     */
//    public function getParticipantsLike(string $term) : ?array
//    {
//        global $ilDB;
//
//        $statement = "
//            SELECT p.id, u.firstname, u.lastname, u.email FROM xvin_participant AS p
//                JOIN usr_data AS u ON p.user_id = u.usr_id
//                WHERE u.firstname LIKE \"%{$term}%\"
//                OR u.lastname LIKE \"%{$term}%\"
//                OR u.email LIKE \"%{$term}%\";
//        ";
//
//        // @TODO: may implement prepared statement here.
//        $result = $ilDB->fetchAll(
//            $ilDB->query($statement)
//        );
//
//        return $result ?? null;
//    }
}
