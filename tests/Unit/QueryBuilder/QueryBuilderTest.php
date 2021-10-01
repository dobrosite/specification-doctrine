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
final class QueryBuilderTest extends TestCase
{
    /**
     * Менеджер сущностей Доктрины.
     *
     * @var EntityManagerInterface&MockObject
     */
    private EntityManagerInterface $entityManager;

    /**
     * Реестр обработчиков спецификаций.
     *
     * @var HandlerRegistry&MockObject
     */
    private HandlerRegistry $handlerRegistry;

    /**
     * Проверяемый составитель запросов DQL.
     */
    private QueryBuilder $queryBuilder;

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
     * Проверяет создание параметров.
     *
     * @throws \Throwable
     */
    public function testCreateParameter(): void
    {
        $placeholder1 = $this->queryBuilder->createParameter('foo', '1');
        $placeholder2 = $this->queryBuilder->createParameter('foo', true, \PDO::PARAM_BOOL);

        self::assertNotEquals($placeholder1, $placeholder2);

        $parameter = $this->queryBuilder->getParameter('foo_1');
        self::assertNotNull($parameter);
        self::assertEquals('1', $parameter->getValue());
        self::assertEquals(\PDO::PARAM_STR, $parameter->getType());

        $parameter = $this->queryBuilder->getParameter('foo_2');
        self::assertNotNull($parameter);
        self::assertTrue($parameter->getValue());
        self::assertEquals(\PDO::PARAM_BOOL, $parameter->getType());
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
