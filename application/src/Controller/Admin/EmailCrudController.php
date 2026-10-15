<?php

namespace App\Controller\Admin;

use App\Entity\Email;
use App\Entity\Log;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailCrudController extends AbstractCrudController
{
    public function __construct(
        private TranslatorInterface $translator
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Email::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // index
        yield IdField::new('id')->onlyOnDetail();
        yield EmailField::new('name');
        yield ChoiceField::new('type')
            ->setChoices(
                [
                    'Уведомления' => Email::EMAIL_NOTIFICATION_TYPE,
                    'Ошибки' => Email::EMAIL_ERROR_TYPE,
                ]
            );
        yield DateTimeField::new('createdAt')->onlyOnIndex()->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])->setPageTitle('detail', 'Email %entity_id%');
    }
}
