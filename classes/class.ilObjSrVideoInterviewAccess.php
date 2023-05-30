<?php

/**
 * Class ilObjSrVideoInterviewAccess
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewAccess extends ilObjectPluginAccess
{
    /**
     * unused, hence not implemented yet.
     *
     * @param string $a_cmd
     * @param string $a_permission
     * @param int    $a_ref_id
     * @param int    $a_obj_id
     * @param string $a_user_id
     * @return bool
     */
    public function _checkAccess(string $cmd, string $permission, int $ref_id, int $obj_id, ?int $user_id = null): bool
    {
        return true;
    }
}
