<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\ConfiguredCheck;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class ConfiguredCheckController extends EasyAdminController
{
    protected function createNewEntity(): ConfiguredCheck
    {
        return new ConfiguredCheck(null, '');
    }
}
