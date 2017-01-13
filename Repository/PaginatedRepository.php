<?php
namespace PaginatorBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * @package Wolnosciowiec\AppBundle\Repository
 */
trait PaginatedRepository
{
    /**
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias, $indexBy = null);

    /**
     * @param array|object $criteria
     * @param QueryBuilder $qb
     * @return bool
     */
    protected function handleFilteringCriteria($criteria, QueryBuilder $qb)
    {
        return false;
    }

    /**
     * @param array|object $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     *
     * @return PaginatedResults
     */
    public function findByPaginated($criteria = [], $orderBy = [], int $limit = 10, int $page = 1) : PaginatedResults
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($orderBy as $key => $direction) {
            $qb->addOrderBy('p.' . $key, $direction);
        }

        if ($this->handleFilteringCriteria($criteria, $qb) === false) {
            foreach ($criteria as $key => $value) {
                if (is_array($value)) {
                    $qb->andWhere('p.' . $key . ' IN (:' . $key . ')');
                } else {
                    $qb->andWhere('p.' . $key . ' = :' . $key);
                }

                $qb->setParameter($key, $value);
            }
        }

        $countQuery = clone $qb;
        $countQuery->select('count(p.id)');
        $maxPages = ceil($countQuery->getQuery()->getSingleScalarResult() / $limit);

        $qb->setMaxResults($limit)
           ->setFirstResult($limit * ($page - 1));

        return new PaginatedResults(
            $qb->getQuery()->getResult(),
            $maxPages,
            $page
        );
    }

    /**
     * @param QueryBuilder $query
     * @param string $alias
     * @return int
     */
    protected function getResultsCount(QueryBuilder $query, $alias)
    {
        $countQuery = clone $query;
        $countQuery->select('count(' . $alias . '.id)');

        return $countQuery->getQuery()->getSingleScalarResult();
    }
}