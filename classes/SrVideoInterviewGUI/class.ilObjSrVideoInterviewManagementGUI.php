<?php

/**
 * Class ilObjSrVideoInterviewManagementGUI
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewManagementGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewManagementGUI
{
    const CMD_MANAGE = 'manage';
    const TAB_NAME   = 'xvin_tab_management';

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
        $cmd = $this->ctrl->getCmd(self::CMD_MANAGE);

        $this->performCommand($cmd);
    }

    public function performCommand(string $cmd)
    {
        switch ($cmd)
        {
            case self::CMD_MANAGE:
                $this->manage();
                break;
            default:
                break;
        }
    }

    public function manage() {
        // do management stuff here.
    }
}