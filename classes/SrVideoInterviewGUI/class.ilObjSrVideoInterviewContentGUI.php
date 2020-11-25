<?php

use srag\Plugins\SrVideoInterview\Repository\ExerciseRepository;

/**
 * Class ilObjSrVideoInterviewContentGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewContentGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewContentGUI
{
    const CMD_INDEX = 'index';
    const TAB_NAME  = 'xvin_tab_content';

    /**
     * @var ilTemplate
     */
    protected $tpl;

    /**
     * @var ilTabsGUI
     */
    protected $tabs;

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    public function __construct()
    {
        global $DIC;

        $this->tpl      = $DIC->ui()->mainTemplate();
        $this->tabs     = $DIC->tabs();
        $this->http     = $DIC->http();
        $this->ctrl     = $DIC->ctrl();
    }

    public function executeCommand() : void
    {
        $this->tabs->activateTab(self::TAB_NAME);
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);

        $this->performCommand($cmd);
    }

    public function performCommand(string $cmd)
    {
        switch ($cmd)
        {
            case self::CMD_INDEX:
                $this->index();
                break;
            default:
                break;
        }
    }

    private function index() : void
    {
        $exercise = new Exercise(
            "title",
            "desc",
            "question lorem ipsdum?",
            "",
            [
                $interview1,
                $interview2,
                $interview3
            ]
        );

        $repo = new ExerciseRepository();


        //        // recordRTC example
        //        $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/node_modules/recordrtc/RecordRTC.js");
        //        $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/script.recordRTC.js");
        //        $this->tpl->addOnLoadCode("il.Plugins.SrVideoInterview.init();");
        //        $tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/tpl.record_rtc.html", false, false);
        //
        //        $this->tpl->setContent($tpl->get());
    }
}