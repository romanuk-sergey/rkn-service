<?php

namespace App\Repository;

use App\Entity\RknResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RknResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method RknResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method RknResponse[]    findAll()
 * @method RknResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RknResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RknResponse::class);
    }

    /**
     * @return RknResponse|null
     */
    public function getActualSocialList(): ?RknResponse
    {
        try {
            $queryBuilder = $this->createQueryBuilder('r');

            $queryBuilder
                ->where('r.type = :social')
                ->setParameter('social', RknResponse::TYPE_SOCIAL_LIST)
                ->orderBy('r.createdAt', 'desc')
                ->setMaxResults(1);

            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $exception) {
            return null;
        }
    }


    /**
     * @return RknResponse|null
     */
    public function getLastDate(): ?RknResponse
    {
        try {
            $queryBuilder = $this->createQueryBuilder('r');

            $queryBuilder
                ->where('r.type = :social')
                ->setParameter('social', RknResponse::TYPE_SOCIAL_DATE)
                ->orderBy('r.createdAt', 'desc')
                ->setMaxResults(1);

            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $exception) {
            return null;
        }
    }
}
