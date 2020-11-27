<?php

use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;

/**
 * Class ilObjSrVideoInterviewContentGUI
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewContentGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewContentGUI
{
    const TAB_NAME  = 'xvin_tab_content';
    const CMD_INDEX = 'index';

    /**
     * @var int
     */
    private $ref_id;

    /**
     * @var int
     */
    private $obj_id;
    
    /**
     * @var ilTemplate
     */
    private $tpl;

    /**
     * @var ilTabsGUI
     */
    private $tabs;

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    private $http;

    /**
     * @var ilCtrl
     */
    private $ctrl;

    /**
     * @var ilAccessHandler
     */
    private $access;

    private $repository;

    /**
     * Initialise ilObjSrVideoInterviewContentGUI
     *
     * @param int $ref_id
     * @param int $obj_id
     */
    public function __construct(int $ref_id, int $obj_id)
    {
        global $DIC;

        $this->repository = new ExerciseRepository();
        $this->tpl      = $DIC->ui()->mainTemplate();
        $this->tabs     = $DIC->tabs();
        $this->http     = $DIC->http();
        $this->ctrl     = $DIC->ctrl();
        $this->access   = $DIC->access();
        $this->ref_id   = $ref_id;
        $this->obj_id   = $obj_id;
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

    private function buildExerciseComponent(Exercise $exercise) : string
    {

        return '';
    }

    /**
     * show exercise entity.
     */
    private function index() : void
    {
        $exercises = $this->repository->getByObjId($this->obj_id);
        if (!empty($exercises)) {
            foreach ($exercises as $exercise) {
                $this->tpl->setContent(
                    ""
                );
            }
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