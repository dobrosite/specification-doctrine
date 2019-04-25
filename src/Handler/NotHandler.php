<?php

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Doctrine\Exception\UnsupportedSpecificationException;
use DobroSite\Specification\Doctrine\HandlerRegistry;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Not;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;

/**
 * Обработчик спецификаций «Not».
 */
class NotHandler implements Handler
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
     * @return Base|Comparison|string
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

        $handler = $this->handlerRegistry->getHandlerFor($specification->getSpecification());
        $nested  = $handler->createCondition($specification->getSpecification(), $queryBuilder);

        return $queryBuilder->expr()->not($nested);
    }

    /**
     * Возвращает имя класса поддерживаемых спецификаций.
     *
     * @return string
     */
    public function getSpecificationClassName()
    {
        return Not::class;
    }
}
