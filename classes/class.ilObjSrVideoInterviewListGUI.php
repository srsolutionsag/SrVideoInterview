<?php

/**
 * Class ilObjSrVideoInterviewListGUI
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilObjSrVideoInterviewListGUI extends ilObjectPluginListGUI
{
    public function getGuiClass()
    {
        return ilObjSrVideoInterviewGUI::class;
    }

    /**
     * @return array
     */
    public function initCommands()
    {
        return [];
    }

    public function initType()
    {
        $this->type = 'xvin';
    }

}
