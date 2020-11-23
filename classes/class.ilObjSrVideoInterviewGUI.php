<?php

require_once __DIR__ . "/class.ilObjSrVideoInterviewManagementGUI.php";

/**
 * Class ilObjSrVideoInterviewGUI
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewManagementGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    const CMD_INDEX     = 'index';
    const CMD_EDIT      = 'edit';
    const CMD_MANAGE    = 'manage';

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;

    /**
     * @var \ilTabsGUI
     */
    protected $tabs;

    /**
     * @var \ilObjSrVideoInterviewAccess
     */
    protected $access_handler;

    /**
     * initialise ilObjSrVideoInterviewGUI
     */
    public function afterConstructor() : void
    {
        global $DIC;

        $this->http   = $DIC->http();
        $this->tabs   = $DIC->tabs();
        // $this->access_handler = new ilObjSrVideoInterviewAccess();

        $this->setupObjectTabs();
    }

    public final function getType() : string
    {
        return ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    public final function getAfterCreationCmd() : string
    {
        return self::CMD_INDEX;
    }

    public final function getStandardCmd() : string
    {
        return self::CMD_INDEX;
    }

    /**
     * @TODO cannot activate ilPermissionGUI Tab, fix
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class)
        {
            case strtolower(ilPermissionGUI::class):
                $perm_gui = new ilPermissionGUI($this); // get existing ilPermissionGUI instead
                $this->tabs->activateTab("id_permissions"); // added by ilObject2GUI
                $this->ctrl->forwardCommand($perm_gui);
                break;
            default:
                break;
        }

        parent::executeCommand();
    }

    public function performCommand(string $cmd) : void
    {
        switch ($cmd) {
            case self::CMD_INDEX:
                $this->tabs->activateTab("xvin_tab_index");
                $this->index();
                break;
            case self::CMD_EDIT:
                $this->tabs->activateTab("xvin_tab_settings");
                $this->index();
                break;
            case self::CMD_MANAGE:
                $this->tabs->activateTab("xvin_tab_management");
                // should this be here?
                $management_gui = new ilObjSrVideoInterviewManagementGUI();
                $management_gui->manage();
                break;
            default:
                break;
        }
    }

    /**
     * creates object tabs and links the corresponding plugin GUI classes.
     *
     * @TODO maybe add tab-ids in a constant
     * @TODO access handling seems not to be working, fix
     */
    protected function setupObjectTabs() : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id))
        {
            // visible for user group >= user
            $this->tabs->addTab(
                "xvin_tab_index",
                $this->txt("obj_xvin_tab_index"),
                $this->ctrl->getLinkTarget($this, self::CMD_INDEX)
            );
        }

        if ($this->access->checkAccess("write", "", $this->ref_id))
        {
            // visible for user group >= editor
            $this->tabs->addTab(
                "xvin_tab_settings",
                $this->txt("obj_xvin_tab_settings"),
                $this->ctrl->getLinkTarget($this, self::CMD_EDIT)
            );

            $this->tabs->addTab(
                "xvin_tab_management",
                $this->txt("obj_xvin_tab_management"),
                $this->ctrl->getLinkTarget($this, self::CMD_MANAGE)
            );
        }
    }

    private function index() : void
    {
        $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/node_modules/recordrtc/RecordRTC.js");
        $this->tpl->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/script.recordRTC.js");
        $this->tpl->addOnLoadCode("il.Plugins.SrVideoInterview.init();");
        $tpl = new ilTemplate("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/tpl.record_rtc.html", false, false);

        $this->tpl->setContent($tpl->get());
    }

}
