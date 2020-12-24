<?php

/**
 * Class ilObjSrVideoInterview
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilObjSrVideoInterview extends ilObjectPlugin
{
    /**
     * sets the plugin repository id
     */
    protected function initType() : void
    {
        $this->type = ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    /**
     * delete all Exercises of the VideoInterview that is about to be deleted.
     *
     * @TODO: implement this method
     */
    protected function doDelete()
    {
        // $this->id is the VideoInterview (obj) id, that is about to be deleted.
    }
}
