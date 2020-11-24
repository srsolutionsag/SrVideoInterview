<?php

require_once __DIR__ . "/VideoInterviewGUI/class.ilObjSrVideoInterviewManagementGUI.php";
require_once __DIR__ . "/VideoInterviewGUI/class.ilObjSrVideoInterviewSettingsGUI.php";
require_once __DIR__ . "/VideoInterviewGUI/class.ilObjSrVideoInterviewContentGUI.php";

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
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewSettingsGUI
 * @ilCtrl_Calls      ilObjSrVideoInterviewGUI: ilObjSrVideoInterviewContentGUI
 */
class ilObjSrVideoInterviewGUI extends ilObjectPluginGUI
{
    /**
     * initialise ilObjSrVideoInterviewGUI
     */
    public function afterConstructor() : void
    {
        // dependencies
    }

    public final function getType() : string
    {
        return ilSrVideoInterviewPlugin::PLUGIN_ID;
    }

    public final function getAfterCreationCmd() : string
    {
        return ilObjSrVideoInterviewContentGUI::CMD_INDEX;
    }

    public final function getStandardCmd() : string
    {
        return ilObjSrVideoInterviewContentGUI::CMD_INDEX;
    }

    /**
     * @TODO cannot activate ilPermissionGUI Tab
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $this->setupObjectTabs();
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class)
        {
            case "":
            case strtolower(ilObjSrVideoInterviewContentGUI::class):
                $content_gui = new ilObjSrVideoInterviewContentGUI();
                $this->ctrl->forwardCommand($content_gui);
                break;
            case strtolower(ilObjSrVideoInterviewSettingsGUI::class):
                $settings_gui = new ilObjSrVideoInterviewSettingsGUI();
                $this->ctrl->forwardCommand($settings_gui);
                break;
            case strtolower(ilObjSrVideoInterviewManagementGUI::class):
                $management_gui = new ilObjSrVideoInterviewManagementGUI();
                $this->ctrl->forwardCommand($management_gui);
                break;
            case strtolower(ilPermissionGUI::class):
                // activate permission tab here.
                break;
            default:
                // we should not reach this
                break;
        }

        parent::executeCommand();
    }

    public function performCommand(string $cmd) : void
    {
        // do nothing actually.
    }

    /**
     * creates object tabs and links the corresponding plugin GUI classes.
     */
    protected function setupObjectTabs() : void
    {
        if ($this->access->checkAccess("read", "", $this->ref_id))
        {
            // visible for user group >= user
            $this->tabs->addTab(
                ilObjSrVideoInterviewContentGUI::TAB_NAME,
                $this->txt(ilObjSrVideoInterviewContentGUI::TAB_NAME),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewContentGUI::class,
                    ilObjSrVideoInterviewContentGUI::CMD_INDEX
                )
            );
        }

        if ($this->access->checkAccess("write", "", $this->ref_id))
        {
            // visible for user group >= editor
            $this->tabs->addTab(
                ilObjSrVideoInterviewSettingsGUI::TAB_NAME,
                $this->txt(ilObjSrVideoInterviewSettingsGUI::TAB_NAME),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewSettingsGUI::class,
                    ilObjSrVideoInterviewSettingsGUI::CMD_EDIT
                )
            );

            $this->tabs->addTab(
                ilObjSrVideoInterviewManagementGUI::TAB_NAME,
                $this->txt(ilObjSrVideoInterviewManagementGUI::TAB_NAME),
                $this->ctrl->getLinkTargetByClass(
                    ilObjSrVideoInterviewManagementGUI::class,
                    ilObjSrVideoInterviewManagementGUI::CMD_MANAGE
                )
            );
        }
    }
}
