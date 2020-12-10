<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component\Input\Field\UploadHandler;
use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\Factory;
use ILIAS\Data\Factory as DataFactory;
use ilToolbarItem;

/**
 * Class File
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class MultiSelectUserInput extends Input implements ilToolbarItem
{
    /**
     * MultiSelectUserInput constructor.
     *
     * @param DataFactory $data_factory
     * @param Factory     $refinery
     * @param string      $label
     */
    public function __construct(DataFactory $data_factory, Factory $refinery, string $label)
    {
        parent::__construct($data_factory, $refinery, $label, null);
    }

    /**
     * @inheritDoc
     */
    protected function getConstraintForRequirement() : ?Constraint
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function isClientSideValueOk($value) : bool
    {
        // TODO: Implement isClientSideValueOk() method.
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateOnLoadCode() : \Closure
    {
        // TODO: Implement getUpdateOnLoadCode() method.
        return static function($i) {};
    }

    /**
     * @inheritDoc
     */
    public function getToolbarHTML() : string
    {
        // TODO: Implement getToolbarHTML() method.
        return '';
    }

    public static function getOne(string $label) : self
    {
        global $DIC;

        $data_factory = new \ILIAS\Data\Factory();
        $refinery = new \ILIAS\Refinery\Factory($data_factory, $DIC["lng"]);

        $DIC["ui.signal_generator"];
        $DIC["ui.factory.input.field"];
        $DIC["ui.factory.input.container"];

        return (new self(
            $data_factory,
            $refinery,
            $label
        ));
    }
}