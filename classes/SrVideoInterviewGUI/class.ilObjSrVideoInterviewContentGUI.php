<?php

require_once __DIR__ . "/../class.ilSrPermissionDeniedException.php";
require_once __DIR__ . "/../class.ilObjSrVideoInterviewGUI.php";

use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;

/**
 * Class ilObjSrVideoInterviewContentGUI
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewContentGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewContentGUI extends ilObjSrVideoInterviewGUI
{
    const TAB_NAME  = 'xvin_tab_content';
    const CMD_INDEX = 'index';

    private $repository;

    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        $this->repository = new ExerciseRepository();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
    }

    /**
     * dispatches the given command and activates current tab.
     *
     * @throws ilSrPermissionDeniedException
     */
    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::TAB_NAME);
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);

        if ($this->access->checkAccess("read", $cmd, $this->ref_id)) {
            $this->$cmd();
        } else {
            throw new ilSrPermissionDeniedException();
        }
    }

    /**
     * show exercise entity.
     */
    private function index() : void
    {
        $exercises = $this->repository->getByObjId($this->obj_id);
        if (!empty($exercises)) {
            $exercises = [];
            foreach ($exercises as $exercise) {
                array_push($exercises, [
                    'title' => $exercise->getTitle(),
                    'description' => $exercise->getDescription(),
                    'detail' => $exercise->getDetailedDescription(),
                ]);
            }

            $listing = $this->ui_factory->listing()->characteristicValue($exercises);
        } else {
            // display error toast instead.
        }
    }

    /**
     * example function for recordRTC js library.
     *
     * @throws ilTemplateException
     */
    private function recordRTC() : void
    {
         // recordRTC example
         $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/node_modules/recordrtc/RecordRTC.js");
         $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/script.recordRTC.js");
         $this->tpl->addOnLoadCode("il.Plugins.SrVideoInterview.init();");
         $tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/tpl.record_rtc.html", false, false);
         $this->tpl->setContent($tpl->get());
    }
}