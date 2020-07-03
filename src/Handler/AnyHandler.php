<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Handler;

use DobroSite\Specification\Any;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификаций «Any».
 *
 * @since 1.2
 */
class AnyHandler implements DoctrineHandler
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
     */
    public function createCondition(Specification $specification, QueryBuilder $queryBuilder)
    {
        if (!$specification instanceof Any) {
            throw new UnsupportedSpecificationException($specification, $this);
        }

        return '1';
    }

    /**
     * Возвращает имя класса поддерживаемых спецификаций.
     *
     * @return string
     */
    public function getSpecificationClassName(): string
    {
        return Any::class;
    }
}
