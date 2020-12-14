<?php

namespace srag\Plugins\SrVideoInterview\UIComponent;

use ILIAS\UI\Implementation\Component\Input\Field\SrVideoInterviewRenderer;
use ILIAS\UI\Component\Component;
use ILIAS\DI\Container;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use ILIAS\UI\Implementation\Component\Input\Field\MultiSelectUserInput;

/**
 * Class Loader
 */
class Loader implements \ILIAS\UI\Implementation\Render\Loader
{
    /**
     * @var Container
     */
    protected $dic;

    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function getRendererFor(Component $component, array $contexts)
    {
        if ($component instanceof VideoRecorderInput || $component instanceof MultiSelectUserInput) {
            return new SrVideoInterviewRenderer(
                $this->dic['ui.factory'],
                $this->dic["ui.template_factory"],
                $this->dic["lng"],
                $this->dic["ui.javascript_binding"],
                $this->dic["refinery"],
            );
        }

        return $this->dic['ui.component_renderer_loader']->getRendererFor($component, $contexts);
    }

    public function getRendererFactoryFor(Component $component)
    {
        return $this->dic['ui.component_renderer_loader']->getRendererFactoryFor($component);
    }
}