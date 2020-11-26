<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview;

/**
 * Interface ReferenceRepository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
interface ReferenceRepository
{
    /**
     * retrieve all references from either side of the reference by given object.
     *
     * @param object $entity
     * @return array
     */
    public function getReferencesForEntity(object $entity) : ?array;

    /**
     * create a new reference for the given object id's or update an existing one.
     *
     * @param int $obj1_id
     * @param int $obj2_id
     * @return bool
     */
    public function store(int $obj1_id, int $obj2_id) : bool;

    /**
     * delete all existing references of the given object from either side of the reference.
     *
     * @param object $entity
     * @return bool
     */
    public function deleteReferencesFromEntity(object $entity) : bool;

    /**
     * delete a specific existing reference by id.
     *
     * @param int $ref_id
     * @return bool
     */
    public function deleteReferenceById(int $ref_id) : bool;
}