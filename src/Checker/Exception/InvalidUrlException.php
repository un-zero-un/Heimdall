<?php

declare(strict_types=1);

namespace App\Checker\Exception;

use App\HeimdallException;

class InvalidUrlException extends \InvalidArgumentException implements HeimdallException
{

}
