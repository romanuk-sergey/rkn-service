<?php

namespace App\Controller\Admin;

use App\Entity\RknResponse;
use App\Repository\RknResponseRepository;
use App\Services\RknSocialListService;
use App\Services\RknSocialListServiceV2;
use Doctrine\ORM\QueryBuilder;
use DomainException;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Routing\Annotation\Route;

class RknResponseCrudController extends AbstractCrudController
{
    public function __construct(
        private RknResponseRepository $rknResponseRepository,
        private AdminUrlGenerator     $adminUrlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return RknResponse::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt', 'Дата создания'));
    }

    public function configureFields(string $pageName): iterable
    {
        // index
        yield IdField::new('id')
            ->onlyOnIndex();
        yield DateTimeField::new('createdAt')
            ->onlyOnIndex();
        yield DateTimeField::new('updateTime')
            ->onlyOnIndex();

        // show
        yield DateTimeField::new('updateTime')
            ->onlyOnDetail();
        yield ArrayField::new('contentDtos')
            ->onlyOnDetail()
            ->setTemplatePath('admin/resources.html.twig')
            ->formatValue(function ($value, $entity) {
                return $entity->getContentDtos();
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
            ->setDefaultSort(['createdAt' => 'DESC'])->setPageTitle('detail', 'Список социально значимых ресурсов %entity_id%');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $query = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $query->andWhere('entity.type = :type')->setParameter('type', 'social-list');

        return $query;
    }

    public function actualSocialList(AdminContext $adminContext)
    {
        $entity = $this->rknResponseRepository->getActualSocialList();
        $rknLastDate = $this->rknResponseRepository->getLastDate();

        if ($entity) {
            return $this->render('admin/social-actual-list.html.twig', [
                'entity' => $entity,
                'fields' => $entity->toArray(),
                'rknLastDate' => $rknLastDate,
            ]);
        }

        $this->addFlash('info', 'Не удалось найти список соц ресурсов');

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }

    /**
     * @param RknSocialListService $rknSocialListService
     * @return string
     */
    #[Route('/manual/actualize-social-list-v1', name: 'actualize-social-list-v1', methods: 'get')]
    public function actualizeSocialListV1(RknSocialListService $rknSocialListService)
    {
        $url = $this->adminUrlGenerator->setController(RknResponseCrudController::class)
            ->setAction('actualSocialList')
            ->generateUrl();

        try {
            $rknSocialListService->syncSocialList();
        } catch (DomainException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirect($url);
    }

    /**
     * @param RknSocialListServiceV2 $rknSocialListService
     * @return string
     */
    #[Route('/manual/actualize-social-list-v2', name: 'actualize-social-list-v2', methods: 'get')]
    public function actualizeSocialListV2(RknSocialListServiceV2 $rknSocialListService)
    {
        $url = $this->adminUrlGenerator->setController(RknResponseCrudController::class)
            ->setAction('actualSocialList')
            ->generateUrl();

        try {
            $rknSocialListService->syncSocialList();
        } catch (DomainException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirect($url);
    }
}
