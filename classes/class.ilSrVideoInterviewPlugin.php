<?php

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/SrVideoInterview/vendor/autoload.php');

use ILIAS\DI\Container;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;
use ILIAS\UI\Implementation\Component\Input\Field\SrVideoInterviewRenderer;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\UI\Implementation\Render\Loader;
use ILIAS\UI\Renderer;
use ILIAS\UI\Implementation\Component\Input\Field\MultiSelectUserInput;

/**
 * Class ilSrVideoInterviewPlugin is the plugin instance
 *
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

    /**
     * @inheritDoc
     * @return string
     */
    final public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * run the plugin deinstall-stuff here, currently removes the AREntity tables.
     */
    final protected function uninstallCustom() : void
    {
        // TODO: Implement uninstallCustom() method, remove database tables
    }

    /**
     * this is currently magic to me, needs to be examined.
     *
     * @param Container $dic
     * @return Closure
     */
    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic) : Closure
    {


        $loader = new class($dic) implements Loader {
            public function __construct(Container $dic)
            {
                $this->dic = $dic;
            }

            public function getRendererFor(Component $component, array $contexts)
            {
                if ($component instanceof VideoRecorderInput ||
                    $component instanceof MultiSelectUserInput
                ) {
                    $renderer = new SrVideoInterviewRenderer(
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
