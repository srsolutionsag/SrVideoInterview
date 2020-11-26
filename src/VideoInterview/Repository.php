<?php

namespace srag\Plugins\SrVideoInterview\VideoInterview;

/**
 * Interface Repository
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
interface Repository
{
    /**
     * delete an existing object in the persistence layer.
     *
     * @param int $obj_id
     * @return bool
     */
    public function delete(int $obj_id) : bool;

    /**
     * store a new object or update an existing one in the persistence layer.
     *
     * @param Object $obj
     * @return bool
     */
    public function store(object $obj) : bool;

    /**
     * retrieve an existing object from the persistence layer if it exists.
     *
     * @param int $obj_id
     * @return object|null
     */
    public function get(int $obj_id) : ?object;

    /**
     * retrieve all existing objects from the persistence layer.
     *
     * @return array
     */
    public function getAll() : array;
}