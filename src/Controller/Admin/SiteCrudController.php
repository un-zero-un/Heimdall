<?php

namespace App\Controller\Admin;

use App\Model\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Site')
            ->setEntityLabelInPlural('Site')
            ->setSearchFields(['id', 'name', 'slug', 'url']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('url'),
            Field::new('id', 'ID')->hideOnForm(),
            TextField::new('slug')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('configuredChecks')->hideOnForm(),
        ];
    }

    public static function getEntityFqcn(): string
    {
        return Site::class;
    }

    public function createEntity(string $entityFqcn): Site
    {
        return new Site('', '');
    }
}
