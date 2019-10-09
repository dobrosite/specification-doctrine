<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Logical\Not;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификаций «Not».
 *
 * @since 1.0
 */
class NotHandler implements DoctrineHandler
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
        if (!$specification instanceof Not) {
            throw new UnsupportedSpecificationException($specification, $this);
        }

        $nestedSpecification = $specification->getSpecifications()[0];
        $handler = $this->handlerRegistry->getHandlerFor(
            $nestedSpecification,
            [DoctrineHandler::class]
        );
        if (!$handler instanceof DoctrineHandler) {
            throw new UnsupportedSpecificationException($nestedSpecification, $handler);
        }

        $nested  = $handler->createCondition($nestedSpecification, $queryBuilder);

        return $queryBuilder->expr()->not($nested);
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
        return Not::class;
    }
}
