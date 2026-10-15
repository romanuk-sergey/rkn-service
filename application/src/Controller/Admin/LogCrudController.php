<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Symfony\Contracts\Translation\TranslatorInterface;

class LogCrudController extends AbstractCrudController
{
    public function __construct(
        private TranslatorInterface      $translator
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Log::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('type')->setChoices([
                'Отправка в телеграмм' => Log::SEND_TELEGRAM_TYPE,
                'Отправка на почту' => Log::SEND_EMAIL_TYPE,
                'Обновление списка' => Log::UPDATE_LIST_TYPE,
                'Автоматический запрос' => Log::AUTO_QUERY_TYPE,
                'Ручной запрос' => Log::MANUAL_QUERY_TYPE,

            ]))
            ->add(DateTimeFilter::new('createdAt', 'Дата создания'));
    }

    public function configureFields(string $pageName): iterable
    {
        // index
        yield IdField::new('id');
        yield TextField::new('type')
            ->formatValue(function ($value, $entity) {
                return $this->translator->trans($value);
            });
        yield DateTimeField::new('createdAt');
        yield TextField::new('data')
            ->onlyOnDetail()
            ->setTemplatePath('admin/log-detail-data.html.twig')
            ->formatValue(function ($value, $entity) {
                if ($entity->getType() === Log::SEND_EMAIL_TYPE) {
                    $email = json_decode($value, true);
                    $email['context'] = json_decode($email['context']);
                    return $email;
                }
                return json_decode($value);
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC'])->setPageTitle('detail', 'Лог %entity_id%');
    }
}
