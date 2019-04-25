<?php

namespace DobroSite\Specification\Doctrine;

use DobroSite\Specification\Doctrine\Exception\UnsupportedSpecificationException;
use DobroSite\Specification\Doctrine\Handler\Handler;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Specification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Поддержка спецификаций для Doctrine.
 *
 * Внимание! Не используйте этот класс в качестве зависимости в своих классах! Вместо него
 * используйте {@see QueryBuilderFactory}.
 *
 * @since 1.0
 */
final class Bridge implements QueryBuilderFactory, HandlerRegistry
{
    /**
     * Обработчики спецификаций.
     *
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * Создаёт составителя запросов для указанного менеджера сущностей.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(EntityManagerInterface $entityManager)
    {
        return new QueryBuilder($entityManager, $this);
    }

    /**
     * Возвращает обработчик для указанной спецификации.
     *
     * @param Specification $specification
     *
     * @return Handler
     *
     * @throws UnsupportedSpecificationException Если переданная спецификация не поддерживается.
     */
    public function getHandlerFor(Specification $specification)
    {
        $className = get_class($specification);
        if (!array_key_exists($className, $this->handlers)) {
            throw new UnsupportedSpecificationException($specification);
        }

        return $this->handlers[$className];
    }

    /**
     * Регистрирует обработчик спецификаций.
     *
     * @param Handler $handler
     *
     * @return void
     *
     * @since 1.0
     */
    public function registerHandler(Handler $handler)
    {
        $this->handlers[$handler->getSpecificationClassName()] = $handler;
    }
}
