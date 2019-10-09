<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Logical\AllOf;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификаций «AllOf».
 *
 * @since 1.0
 */
class AllOfHandler implements DoctrineHandler
{
    /**
     * Реестр обработчиков спецификаций.
     *
     * @var HandlerRegistry
     */
    private $handlerRegistry;

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
     * @param QueryBuilder  $queryBuilder  Составитель запросов.
     *
     * @return Base|Comparison|Func|string
     *
     * @throws UnsupportedSpecificationException Если переданная спецификация не поддерживается.
     *
     * @since 1.0
     */
    public function createCondition(Specification $specification, QueryBuilder $queryBuilder)
    {
        if (!$specification instanceof AllOf) {
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

        return $queryBuilder->expr()->andX(...$parts);
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
        return AllOf::class;
    }
}
