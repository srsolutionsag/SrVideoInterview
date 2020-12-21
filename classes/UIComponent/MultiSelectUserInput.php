<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use iljQueryUtil;
use ilTemplate;
use ilTextInputGUI;
use ilUtil;

/**
 * Class File
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class MultiSelectUserInput extends ilTextInputGUI
{

    /**
     * @var bool
     */
    protected static $instantiated = false;
    /**
     * @var ilTemplate
     */
    protected $global_template;

    /**
     * MultiSelectUserInput constructor.
     * @param string $a_title
     * @param string $a_postvar
     */
    public function __construct($a_title = "", $a_postvar = "")
    {
        global $DIC;
        parent::__construct($a_title, $a_postvar);
        $this->global_template = $DIC->ui()->mainTemplate();
        $this->global_template->addJavaScript("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/default/UIComponent/script.multiSelectUserInput.js");
        $this->global_template->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/css/default/UIComponent/style.multi_select_user_input.css");
        iljQueryUtil::initjQuery();
        iljQueryUtil::initjQueryUI();
    }

    public function render($a_mode = "")
    {
        $id = str_replace('.', '_', uniqid('input_', true));

        $tpl = new ilTemplate(
            SrVideoInterviewRenderer::TEMPLATE_DIR . "tpl.multi_select_user_input.html",
            true,
            true
        );

        if (strlen($this->getValue())) {
            $tpl->setCurrentBlock("prop_text_propval");
            $tpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));
            $tpl->parseCurrentBlock();
        }
        if (strlen($this->getInlineStyle())) {
            $tpl->setCurrentBlock("stylecss");
            $tpl->setVariable("CSS_STYLE", ilUtil::prepareFormOutput($this->getInlineStyle()));
            $tpl->parseCurrentBlock();
        }
        if (strlen($this->getCssClass())) {
            $tpl->setCurrentBlock("classcss");
            $tpl->setVariable('CLASS_CSS', ilUtil::prepareFormOutput($this->getCssClass()));
            $tpl->parseCurrentBlock();
        }
        if ($this->getSubmitFormOnEnter()) {
            $tpl->touchBlock("submit_form_on_enter");
        }

        $tpl->setVariable('PROP_INPUT_TYPE', 'text');
        $tpl->setVariable("ID", $id);
        $this->global_template->addOnLoadCode("il.Plugins.SrMultiUserSearchInputGUI.init('{$id}');");
        $tpl->setVariable("SIZE", $this->getSize());

        if ($this->getMaxLength() != null) {
            $tpl->setVariable("MAXLENGTH", $this->getMaxLength());
        }
        if (strlen($this->getSuffix())) {
            $tpl->setVariable("INPUT_SUFFIX", $this->getSuffix());
        }

        $postvar = $this->getPostVar();
        if ($this->getMulti() && substr($postvar, -2) != "[]") {
            $postvar .= "[]";
        }

        if ($this->getDisabled()) {
            if ($this->getMulti()) {
                $value  = $this->getMultiValues();
                $hidden = "";
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $hidden .= $this->getHiddenTag($postvar, $item);
                    }
                }
            } else {
                $hidden = $this->getHiddenTag($postvar, $this->getValue());
            }
            if ($hidden) {
                $tpl->setVariable("HIDDEN_INPUT", $hidden);
            }
            $tpl->setVariable("DISABLED", " disabled=\"disabled\"");
        } else {
            $tpl->setVariable("POST_VAR", $postvar);
        }

        $tpl->setVariable("URL_AUTOCOMPLETE", $this->getDataSource());

        if ($a_mode == "toolbar") {
            // block-inline hack, see: http://blog.mozilla.com/webdev/2009/02/20/cross-browser-inline-block/
            // -moz-inline-stack for FF2
            // zoom 1; *display:inline for IE6 & 7
            $tpl->setVariable("STYLE_PAR", 'display: -moz-inline-stack; display:inline-block; zoom: 1; *display:inline;');
        } else {
            $tpl->setVariable("STYLE_PAR", '');
        }

        if ($this->isHtmlAutoCompleteDisabled()) {
            $tpl->setVariable("AUTOCOMPLETE", "autocomplete=\"off\"");
        }

        if ($this->getRequired()) {
            $tpl->setVariable("REQUIRED", "required=\"required\"");
        }

        // multi icons
        if ($this->getMulti() && !$a_mode && !$this->getDisabled()) {
            $tpl->touchBlock("inline_in_bl");
            $tpl->setVariable("MULTI_ICONS", $this->getMultiIconsHTML());
        }

        $tpl->setVariable("ARIA_LABEL", ilUtil::prepareFormOutput($this->getTitle()));

        return $tpl->get();
    }

    /**
     * @inheritDoc
     */
    public function getToolbarHTML() : string
    {
        return $this->render("text");
    }

    /**
     * instantiates a new MultiSelectUserInput.
     * @param string $label
     * @param string $postvar
     * @return MultiSelectUserInput
     */
    public static function getInstance(string $label = "", string $postvar = "") : self
    {
        return (new self(
            $label,
            $postvar
        ));
    }
}
