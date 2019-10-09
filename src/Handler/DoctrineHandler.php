<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\Handler;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификаций для Doctrine.
 *
 * @since 1.0
 */
interface DoctrineHandler extends Handler
{
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
    public function createCondition(Specification $specification, QueryBuilder $queryBuilder);
}
