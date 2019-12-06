<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Site;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class SiteController extends EasyAdminController
{
    protected function createNewEntity(): Site
    {
        return new Site('', '');
    }
}
