<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Handler\BasicHandlerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Поддержка спецификаций для Doctrine.
 *
 * Внимание! Не используйте этот класс в качестве зависимости в своих классах! Вместо него
 * используйте {@see QueryBuilderFactory}.
 *
 * @since 1.0
 */
final class Bridge extends BasicHandlerRegistry implements QueryBuilderFactory
{
    /**
     * Создаёт составителя запросов для указанного менеджера сущностей.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(EntityManagerInterface $entityManager): QueryBuilder
    {
        return new QueryBuilder($entityManager, $this);
    }
}
