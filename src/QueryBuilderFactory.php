<?php

namespace DobroSite\Specification\Doctrine;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Фабрика составителей запросов.
 *
 * Пользователям следует использовать этот интерфейс в качестве зависимости для применения
 * спецификаций к запросам.
 *
 * Пример:
 *
 * ```
 * class FooRepository {
 *     // @var QueryBuilderFactory
 *     private $queryBuilderFactory;
 *     // @var EntityManagerInterface
 *     private $entityManager;
 *     // ...
 *     public function find(Specification $specification) {
 *         $queryBuilder = $this->queryBuilderFactory->createQueryBuilder($this->entityManager);
 *         $queryBuilder->from(Foo::class, 'f')->select('f')->match($specification);
 *         return $queryBuilder->getQuery()->getResult();
 *     }
 * }
 * ```
 *
 * @since 1.0
 */
interface QueryBuilderFactory
{
    /**
     * Создаёт составителя запросов для указанного менеджера сущностей.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return QueryBuilder
     *
     * @since 1.0
     */
    public function createQueryBuilder(EntityManagerInterface $entityManager);
}
