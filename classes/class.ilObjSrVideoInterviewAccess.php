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
    public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = "")
    {
        return true;
    }
}
