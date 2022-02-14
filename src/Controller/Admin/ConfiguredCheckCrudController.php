<?php

namespace App\Controller\Admin;

use App\Form\Type\CheckerChoiceType;
use App\Form\Type\CheckerConfiguration\CheckerConfigType;
use App\Model\ConfiguredCheck;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfiguredCheckCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConfiguredCheck::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('ConfiguredCheck')
            ->setEntityLabelInPlural('ConfiguredCheck')
            ->setSearchFields(['id', 'check', 'executionDelay', 'config']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id', 'ID')->hideOnForm(),
            AssociationField::new('site'),
            TextField::new('check')
                     ->setTemplatePath('admin/configured_check/_checker.html.twig')
                     ->setFormType(CheckerChoiceType::class),
            IntegerField::new('executionDelay'),
            ArrayField::new('config')->hideOnIndex()->setFormType(CheckerConfigType::class),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }

    public function createEntity(string $entityFqcn): ConfiguredCheck
    {
        return new ConfiguredCheck(null, '');
    }
}
