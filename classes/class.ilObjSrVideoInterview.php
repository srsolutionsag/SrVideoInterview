<?php

/**
 * Class ilObjSrVideoInterview
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterview extends ilObjectPlugin
{
    protected function initType()
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

}
