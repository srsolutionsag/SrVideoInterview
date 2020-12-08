<?php

use ILIAS\DI\Container;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderRenderer;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\UI\Implementation\Render\Loader;
use ILIAS\UI\Renderer;

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/vendor/autoload.php');

/**
 * Class ilSrVideoInterviewPlugin
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilSrVideoInterviewPlugin extends ilRepositoryObjectPlugin
{
    /**
     * @var string
     */
    const PLUGIN_ID = 'xvin';

    /**
     * @var string
     */
    const PLUGIN_NAME = 'SrVideoInterview';

    /**
     * @var ilSrVideoInterviewPlugin
     */
    protected static $instance;

    /**
     * @return static
     */
    final public static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    final public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    final protected function uninstallCustom() : void
    {
        // TODO: Implement uninstallCustom() method, remove database tables
    }

    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic) : Closure
    {
        $loader = new class($dic) implements Loader {
            public function __construct(Container $dic)
            {
                $this->dic = $dic;
            }

            public function getRendererFor(Component $component, array $contexts)
            {
                if ($component instanceof VideoRecorderInput) {
                    $renderer = new VideoRecorderRenderer(
                        $this->dic['ui.factory'],
                        $this->dic["ui.template_factory"],
                        $this->dic["lng"],
                        $this->dic["ui.javascript_binding"],
                        $this->dic["refinery"],
                    );
                    $renderer->registerResources($this->dic["ui.resource_registry"]);
                    return $renderer;
                }
                return $this->dic['ui.component_renderer_loader']->getRendererFor($component, $contexts);
            }

            public function getRendererFactoryFor(Component $component)
            {
                return $this->dic['ui.component_renderer_loader']->getRendererFactoryFor($component);
            }

        };
        return function ($dic) use ($loader) {

            return new class($loader) extends DefaultRenderer implements Renderer {

            };
        };

    }

}
