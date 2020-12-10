<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component\Input\Field\UploadHandler;

/**
 * Class File
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class VideoRecorderInput extends File
{

    protected function getConstraintForRequirement()
    {
        return $this->refinery->string();
    }

    protected function isClientSideValueOk($value) : bool
    {
        return true; // TODO prüfen, ob $value irgendwas valiudes ist...
    }

    public function getUpdateOnLoadCode() : \Closure
    {
        return static function () {
        };
    }

    public static function getOne(
        UploadHandler $upload_handler,
        string $lable,
        string $byline = null
    ) : VideoRecorderInput {
        global $DIC;
        $data_factory = new \ILIAS\Data\Factory();
        $refinery = new \ILIAS\Refinery\Factory($data_factory, $DIC["lng"]);

        $DIC["ui.signal_generator"];
        $DIC["ui.factory.input.field"];
        $DIC["ui.factory.input.container"];

        return (new self(
            $data_factory,
            $refinery,
            $upload_handler,
            $lable,
            $byline
        ));
    }
}
