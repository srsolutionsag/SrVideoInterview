<?php

/**
 * Class ilSrPermissionDeniedException
 *
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
class ilSrPermissionDeniedException extends Exception
{
    /**
     * ilSrPermissionDeniedException constructor
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Permission denied", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}