<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component\Input\Field\UploadHandler;
use ILIAS\Refinery\Factory;
use ILIAS\UI\Component as C;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\UI\Implementation\Component\Input\InputData;

/**
 * Class File
 *
 * @package ILIAS\UI\Implementation\Component\Input\Field
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class VideoRecorderInput extends File
{
    /**
     * VideoRecorderInput constructor.
     * @param DataFactory   $data_factory
     * @param Factory       $refinery
     * @param UploadHandler $handler
     * @param string        $label
     */
    public function __construct(
        DataFactory $data_factory,
        Factory $refinery,
        C\Input\Field\UploadHandler $handler,
        string $label
    ) {
        parent::__construct($data_factory, $refinery, $handler, $label, null);
    }

    /**
     * @inheritDoc
     */
    protected function getConstraintForRequirement()
    {
        return $this->refinery->string();
    }

    /**
     * @inheritDoc
     */
    protected function isClientSideValueOk($value) : bool
    {
        // @TODO: prove if value is correct
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateOnLoadCode() : \Closure
    {
        return static function () {};
    }

    /**
     * get a VideoRecorderInput instance
     * @param UploadHandler $upload_handler
     * @param string        $label
     * @return VideoRecorderInput
     */
    public static function getInstance(
        UploadHandler $upload_handler,
        string $label
    ) : VideoRecorderInput {
        global $DIC;

        $data_factory = new \ILIAS\Data\Factory();
        $refinery = new \ILIAS\Refinery\Factory($data_factory, $DIC["lng"]);

        return (new self(
            $data_factory,
            $refinery,
            $upload_handler,
            $label
        ));
    }
}
