<?php

//require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewManagementGUI.php";
//require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewSettingsGUI.php";
//require_once __DIR__ . "/SrVideoInterviewGUI/class.ilObjSrVideoInterviewContentGUI.php";

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
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;

    /**
     * @var ILIAS\Refinery\Factory
     */
    protected $refinery;

    /**
     * @var \ILIAS\UI\Factory
     */
    protected $ui_factory;

    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $ui_renderer;

    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        $this->ui_factory  = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->refinery    = $DIC->refinery();
        $this->http        = $DIC->http();

        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
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
     * needs to be implemented because of ilObjectPluginGUI.
     *
     * @param string $cmd
     */
    public function performCommand(string $cmd) : void
    {
        // does nothing.
    }

    /**
     * @TODO: activate ilPermissionGUI tab, not working yet.
     * @throws ilCtrlException
     */
    public function executeCommand() : void
    {
        $this->setupObjectTabs();
        $next_class = $this->ctrl->getNextClass($this);
        switch ($next_class)
        {
            case '':
            case strtolower(ilObjSrVideoInterviewContentGUI::class):
                if ($this->getCreationMode()) break;
                $content_gui = new ilObjSrVideoInterviewContentGUI($this->ref_id, $this->obj_id);
                $this->ctrl->forwardCommand($content_gui);
                break;
            case strtolower(ilObjSrVideoInterviewSettingsGUI::class):
                $settings_gui = new ilObjSrVideoInterviewSettingsGUI($this->ref_id);
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
                // we should not reach this.
                break;
        }

        parent::executeCommand();
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
                    ilObjSrVideoInterviewSettingsGUI::CMD_SETTINGS_SHOW
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

    /**
     * retrieve rendered error-message for a custom message.
     *
     * @param string $msg
     * @return string
     */
    protected function renderErrorMessage(string $msg) : string
    {
        return $this->ui_renderer->render(
            $this->ui_factory->messageBox()->failure($msg)
        );
    }

    /**
     * retrieve rendered success-message for a custom message.
     *
     * @param string $msg
     * @return string
     */
    protected function renderSuccessMessage(string $msg) : string
    {
        return $this->ui_renderer->render(
            $this->ui_factory->messageBox()->success($msg)
        );
    }
}
