<?php

namespace DobroSite\Specification\Doctrine\QueryBuilder;

use DobroSite\Specification\Doctrine\HandlerRegistry;
use DobroSite\Specification\Specification;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * Составитель запросов DQL на основе спецификаций.
 *
 * @method self add($dqlPartName, $dqlPart, $append = false)
 * @method self addCriteria(Criteria $criteria)
 * @method self addGroupBy($groupBy)
 * @method self addOrderBy($sort, $order = null)
 * @method self addSelect($select = null)
 * @method self andHaving($having)
 * @method self andWhere()
 * @method self delete($delete = null, $alias = null)
 * @method self distinct($flag = true)
 * @method self from($from, $alias, $indexBy = null)
 * @method self groupBy($groupBy)
 * @method self having($having)
 * @method self innerJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
 * @method self leftJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
 * @method self orderBy($sort, $order = null)
 * @method self orHaving($having)
 * @method self orWhere()
 * @method self select($select = null)
 * @method self update($update = null, $alias = null)
 * @method self where($predicates)
 *
 * @since 1.0
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    /**
     * Псевдонимы для используемых классов сущностей.
     *
     * @var string[]
     */
    private $aliases = [];

    /**
     * Реестр обработчиков спецификаций.
     *
     * @var HandlerRegistry
     */
    private $handlerRegistry;

    /**
     * Создаёт построитель запросов.
     *
     * @param EntityManagerInterface $em
     * @param HandlerRegistry        $handlerRegistry
     *
     * @since 1.0
     */
    public function __construct(EntityManagerInterface $em, HandlerRegistry $handlerRegistry)
    {
        parent::__construct($em);

        $this->handlerRegistry = $handlerRegistry;
    }

    /**
     * Возвращает постоянный псевдоним для указанного класса сущностей.
     *
     * @param string $entityClass
     *
     * @return string
     *
     * @since 1.0
     */
    public function getAliasFor($entityClass)
    {
        // Вначале ищем среди корневых псевдонимов.
        foreach ($this->getDQLPart('from') as $clause) {
            if (is_string($clause)) {
                // Если $fromClause не объект, псевдоним нам не узнать :-(
                continue;
            }

            if ($clause instanceof From && $clause->getFrom() === $entityClass) {
                return $clause->getAlias();
            }
        }

        // Если ничего не нашли, создаём псевдоним сами.
        if (!array_key_exists($entityClass, $this->aliases)) {
            $parts = explode('\\', $entityClass);
            $className = end($parts);

            // $className делает имена более читаемыми.
            // count($this->aliases) предотвращает конфликты.
            $this->aliases[$entityClass] = strtolower($className) . count($this->aliases);
        }

        return $this->aliases[$entityClass];
    }

    /**
     * Добавляет часть JOIN.
     *
     * В отличие от родительского класса, оператор JOIN добавляется только если он не был добавлен
     * ранее.
     *
     * @param string      $join
     * @param string      $alias
     * @param string|null $conditionType
     * @param string|null $condition
     * @param string|null $indexBy
     *
     * @return $this
     *
     * @since 1.0
     */
    public function join(
        $join,
        $alias,
        $conditionType = null,
        $condition = null,
        $indexBy = null
    ) {
        $parts = $this->getDQLPart('join');

        if (is_array($parts)) {
            foreach ($parts as $expressions) {
                if (!is_array($expressions)) {
                    continue;
                }
                foreach ($expressions as $expression) {
                    if ($expression instanceof Join && $expression->getAlias() === $alias) {
                        return $this;
                    }
                }
            }
        }

        parent::join($join, $alias, $conditionType, $condition, $indexBy);

        return $this;
    }

    /**
     * Применяет спецификацию к запросу.
     *
     * @param Specification $specification
     *
     * @return $this
     *
     * @since 1.0
     */
    public function match(Specification $specification)
    {
        $handler = $this->handlerRegistry->getHandlerFor($specification);

        $condition = $handler->createCondition($specification, $this);
        $this->where($condition);

        return $this;
    }
}
