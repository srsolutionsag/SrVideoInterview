<?php

/**
 * Class ilObjSrVideoInterviewAccess
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewAccess extends ilObjectPluginAccess
{
    public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = "")
    {
        return true;
    }
}
