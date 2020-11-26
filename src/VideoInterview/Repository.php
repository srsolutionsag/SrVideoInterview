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
     * delete an existing object by its id.
     *
     * @param int $obj_id
     * @return bool
     */
    public function delete(int $obj_id) : bool;

    /**
     * create a new object or update an existing one.
     *
     * @param Object $obj
     * @return bool
     */
    public function store(object $obj) : bool;

    /**
     * retrieve an existing object by it's id or null.
     *
     * @param int $obj_id
     * @return object|null
     */
    public function get(int $obj_id) : ?object;

    /**
     * retrieve all existing objects in an array or null.
     *
     * @return array|null
     */
    public function getAll() : ?array;
}