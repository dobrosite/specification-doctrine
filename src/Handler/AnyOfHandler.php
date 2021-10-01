<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Logical\AnyOf;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификаций «AnyOf».
 *
 * @since 1.0
 */
class AnyOfHandler implements DoctrineHandler
{
    /**
     * Реестр обработчиков спецификаций.
     */
    private HandlerRegistry $handlerRegistry;

    /**
     * Задаёт обработчик спецификаций.
     *
     * @param HandlerRegistry $handlerRegistry
     *
     * @since 1.0
     */
    public function __construct(HandlerRegistry $handlerRegistry)
    {
        $this->handlerRegistry = $handlerRegistry;
    }

    /**
     * Создаёт условие на основе спецификации.
     *
     * @param Specification $specification Спецификация.
     * @param QueryBuilder  $queryBuilder  Построитель запросов.
     *
     * @return Base|Comparison|Func|string
     *
     * @throws UnsupportedSpecificationException Если переданная спецификация не поддерживается.
     *
     * @since 1.0
     */
    public function createCondition(Specification $specification, QueryBuilder $queryBuilder)
    {
        if (!$specification instanceof AnyOf) {
            throw new UnsupportedSpecificationException($specification, $this);
        }

        $parts = [];
        foreach ($specification->getSpecifications() as $nestedSpecification) {
            $handler = $this->handlerRegistry->getHandlerFor(
                $nestedSpecification,
                [DoctrineHandler::class]
            );
            if (!$handler instanceof DoctrineHandler) {
                throw new UnsupportedSpecificationException($specification, $handler);
            }
            $parts[] = $handler->createCondition($nestedSpecification, $queryBuilder);
        }

        return $queryBuilder->expr()->orX(...$parts);
    }

    /**
     * Возвращает имя класса поддерживаемых спецификаций.
     *
     * @return string
     *
     * @since 1.0
     */
    public function getSpecificationClassName(): string
    {
        return AnyOf::class;
    }
}
