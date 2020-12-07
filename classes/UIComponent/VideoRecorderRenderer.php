<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\ilTemplateWrapper;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;

/**
 * Class VideoRecorderRenderer
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class VideoRecorderRenderer extends Renderer
{
    protected function renderInputField(Template $tpl, Input $input, $id, RendererInterface $default_renderer)
    {
        $tpl->setVariable('ID', $id);

        return $tpl->get();
    }

    public function registerResources(ResourceRegistry $registry)
    {
        $registry->register('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/node_modules/recordrtc/RecordRTC.min.js');
        $registry->register('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/js/script.recordRTC.js');
    }

    /**
     * @param VideoRecorderInput $component
     * @param RendererInterface  $default_renderer
     * @return string
     */
    public function render(Component $component, RendererInterface $default_renderer)
    {
        global $DIC;

        $component = $component->withOnLoadCode(function ($id) use ($component) {
            return 'il.Plugins.SrVideoInterview.init("' . $id . '", "' . $component->getVideoRecoderURL() . '")';
        });

        $id = $this->bindJavaScript($component);

        $input_tpl = new ilTemplateWrapper(
            $DIC->ui()->mainTemplate(),
            new \ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/templates/tpl.record_rtc.html', false, false)
        );

        return $this->renderInputFieldWithContext($default_renderer, $input_tpl, $component, $id);
    }

    protected function getComponentInterfaceName()
    {
        return [
            VideoRecorderInput::class
        ];
    }

}
