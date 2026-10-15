<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // index
        yield TextField::new('username')
            ->onlyOnIndex();
        yield ChoiceField::new('roles')
            ->setChoices(User::getRolesList())
            ->onlyOnIndex();

        // new
        yield TextField::new('username')
            ->onlyWhenCreating();
        yield TextField::new('password')->onlyWhenCreating();
        yield ChoiceField::new('roles')
            ->allowMultipleChoices(true)
            ->setChoices(User::getRolesList())
            ->onlyWhenCreating();

        // edit
        yield TextField::new('username')
            ->onlyWhenUpdating();
        yield TextField::new('newPassword', 'Password')
            ->onlyWhenUpdating();
        yield ChoiceField::new('roles')
            ->allowMultipleChoices(true)
            ->setChoices(User::getRolesList())
            ->onlyWhenUpdating();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission(User::ROLE_ADMIN);
    }
}
