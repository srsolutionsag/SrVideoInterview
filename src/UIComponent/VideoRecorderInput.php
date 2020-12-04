<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

/**
 * Class File
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class VideoRecorderInput extends Input
{
    protected function getConstraintForRequirement()
    {
        return $this->refinery->string();
    }

    protected function isClientSideValueOk($value) : bool
    {
        return true;
    }

    public function getUpdateOnLoadCode() : \Closure
    {
        return static function () {
        };
    }

}
