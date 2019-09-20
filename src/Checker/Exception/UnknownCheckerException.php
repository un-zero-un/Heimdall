<?php

declare(strict_types=1);

namespace App\Checker\Exception;

use App\HeimdallException;

class UnknownCheckerException extends \OutOfBoundsException implements HeimdallException
{
    public function __construct(string $checkerName)
    {
        parent::__construct(sprintf('Checker "%s" is unknown.', $checkerName));
    }
}
