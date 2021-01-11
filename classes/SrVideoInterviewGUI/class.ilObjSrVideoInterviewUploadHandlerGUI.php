<?php

use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Handler\AbstractCtrlAwareUploadHandler;
use ILIAS\FileUpload\Handler\BasicFileInfoResult;
use ILIAS\FileUpload\Handler\BasicHandlerResult;
use ILIAS\FileUpload\Handler\FileInfoResult;
use ILIAS\FileUpload\Handler\HandlerResult as HandlerResultInterface;
use ILIAS\MainMenu\Storage\Services;
use ILIAS\MainMenu\Storage\Resource\Stakeholder\AbstractResourceStakeholder;
use ILIAS\MainMenu\Storage\Resource\Stakeholder\ResourceStakeholder;

/**
 * Class ilObjSrVideoInterviewUploadHandlerGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjSrVideoInterviewUploadHandlerGUI: ilObjSrVideoInterviewGUI
 */
class ilObjSrVideoInterviewUploadHandlerGUI extends AbstractCtrlAwareUploadHandler
{
    const CMD_DOWNLOAD = 'download';

    /**
     * @var Services
     */
    private $storage;
    /**
     * @var ResourceStakeholder
     */
    private $stakeholder;

    /**
     * ilUIDemoFileUploadHandlerGUI constructor.
     */
    public function __construct()
    {
        $this->storage = new Services();
        $this->stakeholder = new class extends AbstractResourceStakeholder {
            /**
             * @return string
             */
            public function getId() : string
            {
                return ilSrVideoInterviewPlugin::PLUGIN_ID;
            }
        };

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function executeCommand() : void
    {
        switch ($this->ctrl->getCmd()) {
            case self::CMD_DOWNLOAD:
                $file_identifier = $this->http->request()->getQueryParams()[$this->getFileIdentifierParameterName()];
                $this->downloadExistingFile($file_identifier);
                break;
            default:
                break;
        }

        parent::executeCommand();
    }

    /**
     * get CMD_DOWNLOAD url
     *
     * @return string
     */
    public function getExistingFileDownloadURL() : string
    {
        return $this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_DOWNLOAD,
            "",
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function getUploadURL() : string
    {
        // override upload link to enable async.
        return $this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_UPLOAD,
            "",
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function getFileRemovalURL() : string
    {
        // override upload link to enable async.
        return $this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_REMOVE,
            "",
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function getExistingFileInfoURL() : string
    {
        // override upload link to enable async.
        return $this->ctrl->getLinkTargetByClass(
            self::class,
            self::CMD_INFO,
            "",
            true
        );
    }

    /**
     * @param string $identifier
     */
    protected function downloadExistingFile(string $identifier) : void
    {
        $identification = $this->storage->find($identifier);

        if (null !== $identification) {
            $file = $this->storage->inline($identification);
            $file->run();
        }

        // what on err?
    }

    /**
     * @inheritDoc
     */
    protected function getUploadResult() : HandlerResultInterface
    {
        $this->upload->process();
        /**
         * @var $result UploadResult
         */
        $array = $this->upload->getResults();
        $result = end($array);
        if ($result instanceof UploadResult && $result->isOK()) {
            $i = $this->storage->upload($result, $this->stakeholder);
            $status = HandlerResultInterface::STATUS_OK;
            $identifier = $i->serialize();
            $message = 'upload ok';
        } else {
            $status = HandlerResultInterface::STATUS_FAILED;
            $identifier = '';
            $message = $result->getStatus()->getMessage();
        }

        return new BasicHandlerResult($this->getFileIdentifierParameterName(), $status, $identifier, $message);
    }

    /**
     * @param string $identifier
     * @return HandlerResultInterface
     */
    protected function getRemoveResult(string $identifier) : HandlerResultInterface
    {
        $id = $this->storage->find($identifier);
        if ($id !== null) {

            $this->storage->stream($id)->getStream();
            $this->storage->remove($id);
            return new BasicHandlerResult(
                $this->getFileIdentifierParameterName(),
                HandlerResultInterface::STATUS_OK,
                $identifier,
                'file deleted'
            );
        }

        return new BasicHandlerResult(
            $this->getFileIdentifierParameterName(),
            HandlerResultInterface::STATUS_FAILED,
            $identifier,
            'file not found'
        );
    }

    /**
     * @param string $identifier
     * @return FileInfoResult
     */
    protected function getInfoResult(string $identifier) : FileInfoResult
    {
        $id = $this->storage->find($identifier);

        if ($id === null) {
            return new BasicFileInfoResult(
                $this->getFileIdentifierParameterName(),
                'unknown',
                'unknown',
                0,
                'unknown'
            );
        }

        $resource = $this->storage->getRevision($id)->getInformation();
        return new BasicFileInfoResult(
            $this->getFileIdentifierParameterName(),
            $identifier,
            $resource->getTitle(),
            $resource->getSize(),
            $resource->getMimeType()
        );
    }

    /**
     * @param array $file_ids
     * @return array
     */
    public function getInfoForExistingFiles(array $file_ids) : array
    {
        $infos = [];
        foreach ($file_ids as $file_id) {
            $id = $this->storage->find($file_id);
            if ($id === null) {
                continue;
            }

            $resource = $this->storage->getRevision($id)->getInformation();
            $infos[] = new BasicFileInfoResult(
                $this->getFileIdentifierParameterName(),
                $file_id,
                $resource->getTitle(),
                $resource->getSize(),
                $resource->getMimeType()
            );
        }

        return $infos;
    }
}
