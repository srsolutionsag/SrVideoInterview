<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\ilTemplateWrapper;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use stdClass;
use ilTemplate;
use ilTemplateException;

/**
 * Class SrVideoInterviewRenderer
 *
 * @TODO: either implement multiSelectUserInput as UIComponent or remove commented sections for this input.
 *
 * @package ILIAS\UI\Implementation\Component\Input\Field
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class SrVideoInterviewRenderer extends Renderer
{
    const JAVASCRIPT_DIR = './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/default/UIComponent/';
    const TEMPLATE_DIR   = './Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/default/UIComponent/';

    /**
     * @param Template          $tpl
     * @param Input             $input
     * @param                   $id
     * @param RendererInterface $default_renderer
     * @return string
     */
    protected function renderInputField(Template $tpl, Input $input, $id, RendererInterface $default_renderer) : string
    {
        $tpl->setVariable("NAME", $input->getName());
        $tpl->setVariable("VALUE", $input->getValue());
        $tpl->setVariable('LABEL', $input->getLabel());
        $tpl->setVariable('ID', $id);

        return $tpl->get();
    }

    /**
     * @param ResourceRegistry $registry
     * @return void|null
     */
    public function registerResources(ResourceRegistry $registry)
    {
        // override renderer defaults
    }

    /**
     * @param Component         $component
     * @param RendererInterface $default_renderer
     * @return string
     * @throws ilTemplateException
     */
    public function render(Component $component, RendererInterface $default_renderer) : string
    {
        global $DIC;

        $registry = $DIC["ui.resource_registry"];
        $global_template = $DIC->ui()->mainTemplate();

        $settings = new stdClass();
        if ($component instanceof VideoRecorderInput) {
            $settings->file_identifier_key = $component->getUploadHandler()->getFileIdentifierParameterName();
            $settings->accepted_files = implode(',', $component->getAcceptedMimeTypes());
            $settings->upload_url   = $component->getUploadHandler()->getUploadURL();
            $settings->removal_url  = $component->getUploadHandler()->getFileRemovalURL();
            $settings->download_url = $component->getUploadHandler()->getExistingFileDownloadURL();

            $global_template->addCss("./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/css/default/UIComponent/style.video_recorder_input.css");
            $registry->register('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/node_modules/recordrtc/RecordRTC.min.js');
            $registry->register(self::JAVASCRIPT_DIR . "script.videoRecorderInput.js");

            $input_tpl = new ilTemplateWrapper(
                $global_template,
                new ilTemplate(
                    self::TEMPLATE_DIR . "tpl.video_recorder_input.html",
                    true,
                    true
                )
            );
        }
//        elseif ($component instanceof MultiSelectUserInput) {
//            $registry->register(self::JAVASCRIPT_DIR . "script.multiSelectUserInput.js");
//
//            $input_tpl = new ilTemplateWrapper(
//                $global_template,
//                new ilTemplate(
//                    self::TEMPLATE_DIR . "tpl.multi_select_user_input.html",
//                    false,
//                    false
//                )
//            );
//        }
        else {
            throw new ilTemplateException("could not determine component type of: " . get_class($component));
        }

        $component = $component->withOnLoadCode(
            static function($id) use ($settings) {
                $settings = json_encode($settings);
                return "il.Plugins.SrVideoInterview.init('{$id}', '{$settings}')";
            }
        );

        $id = $this->bindJavaScript($component);

        return $this->renderInputFieldWithContext(
            $default_renderer,
            $input_tpl,
            $component,
            $id
        );
    }

    /**
     * @return string[]
     */
    protected function getComponentInterfaceName() : array
    {
        return array(
            VideoRecorderInput::class,
//            MultiSelectUserInput::class,
        );
    }

}
