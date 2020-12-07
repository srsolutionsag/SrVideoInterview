<?php

namespace ILIAS\UI\Implementation\Component\Input\Field;

/**
 * Class File
 * @package ILIAS\UI\Implementation\Component\Input\Field
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class VideoRecorderInput extends Input
{

    protected string $video_recorder_url = '';

    public function withVideoRecoderURL(string $url) : self
    {
        $clone                     = clone $this;
        $clone->video_recorder_url = $url;

        return $clone;
    }

    public function getVideoRecoderURL() : string
    {
        return $this->video_recorder_url;
    }

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

    public static function getOne(string $upload_url, string $lable, string $byline = null) : VideoRecorderInput
    {
        global $DIC;
        $data_factory = new \ILIAS\Data\Factory();
        $refinery     = new \ILIAS\Refinery\Factory($data_factory, $DIC["lng"]);

        $DIC["ui.signal_generator"];
        $DIC["ui.factory.input.field"];
        $DIC["ui.factory.input.container"];

        return (new self(
            $data_factory,
            $refinery,
            $lable,
            $byline
        ))->withVideoRecoderURL($upload_url);
    }
}
