<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Tests\Unit\QueryBuilder;

use DobroSite\Specification\Doctrine\Handler\DoctrineHandler;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Specification;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Тесты Составитель запросов DQL
 *
 * @covers \DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    /**
     * Менеджер сущностей Доктрины.
     *
     * @var EntityManagerInterface&MockObject
     */
    private $entityManager;

    /**
     * Реестр обработчиков спецификаций.
     *
     * @var HandlerRegistry&MockObject
     */
    private $handlerRegistry;

    /**
     * Проверяемый составитель запросов DQL.
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Проверяет создание псевдонима.
     *
     * @throws \Throwable
     */
    public function testCreateAlias(): void
    {
        $alias1 = $this->queryBuilder->getAliasFor('Foo');
        $alias2 = $this->queryBuilder->getAliasFor('Foo');

        self::assertEquals('foo0', $alias1);
        self::assertEquals($alias1, $alias2);
    }

    /**
     * Проверяет получение псевдонима.
     *
     * @throws \Throwable
     */
    public function testGetAlias(): void
    {
        $this->queryBuilder->from('Foo', 'f');

        $alias1 = $this->queryBuilder->getAliasFor('Foo');
        $alias2 = $this->queryBuilder->getAliasFor('Foo');

        self::assertEquals('f', $alias1);
        self::assertEquals($alias1, $alias2);
    }

    /**
     * Проверяет применение спецификации.
     *
     * @throws \Throwable
     */
    public function testMatch(): void
    {
        $spec = $this->createMock(Specification::class);
        $handler = $this->createMock(DoctrineHandler::class);

        $this->handlerRegistry
            ->expects(self::once())
            ->method('getHandlerFor')
            ->with(self::identicalTo($spec))
            ->willReturn($handler);

        $handler
            ->expects(self::once())
            ->method('createCondition')
            ->with(self::identicalTo($spec), self::identicalTo($this->queryBuilder))
            ->willReturn('X = Y');

        $queryBuilder = $this->queryBuilder->match($spec);

        self::assertSame($this->queryBuilder, $queryBuilder);
        self::assertEquals('SELECT WHERE X = Y', $this->queryBuilder->getDQL());
    }

    /**
     * Проверяет что операторы JOIN не добавляются повторно.
     *
     * @throws \Throwable
     */
    public function testJoinsNotDuplicated(): void
    {
        $this->queryBuilder
            ->from('Foo', 'f')
            ->select('f')
            ->join('Bar', 'b')
            ->join('Bar', 'b');

        self::assertEquals('SELECT f FROM Foo f INNER JOIN Bar b', $this->queryBuilder->getDQL());
    }

    /**
     * Готовит окружение теста.
     *
     * @throws \Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handlerRegistry = $this->createMock(HandlerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->queryBuilder = new QueryBuilder($this->entityManager, $this->handlerRegistry);
    }
}
