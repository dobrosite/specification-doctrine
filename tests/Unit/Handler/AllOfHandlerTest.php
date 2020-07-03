<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Tests\Unit\Handler;

use DobroSite\Specification\Doctrine\Handler\AllOfHandler;
use DobroSite\Specification\Doctrine\Handler\DoctrineHandler;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Logical\AllOf;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Тесты обработчика спецификаций «AllOf».
 *
 * @covers \DobroSite\Specification\Doctrine\Handler\AllOfHandler
 */
class AllOfHandlerTest extends TestCase
{
    /**
     * Проверяемый обработчик.
     *
     * @var AllOfHandler
     */
    private $handler;

    /**
     * Реестр обработчиков спецификаций.
     *
     * @var HandlerRegistry&MockObject
     */
    private $handlerRegistry;

    /**
     * Составитель запросов.
     *
     * @var QueryBuilder&MockObject
     */
    private $queryBuilder;

    /**
     * Проверяет создание выражения из спецификации.
     *
     * @throws \Throwable
     */
    public function testCreateExpression(): void
    {
        $spec1 = $this->createConfiguredMock(Specification::class, []);
        $spec2 = $this->createConfiguredMock(Specification::class, []);

        $handler1 = $this->createMock(DoctrineHandler::class);
        $handler2 = $this->createMock(DoctrineHandler::class);

        $this->handlerRegistry
            ->expects(self::exactly(2))
            ->method('getHandlerFor')
            ->willReturnMap(
                [
                    [$spec1, [DoctrineHandler::class], $handler1],
                    [$spec2, [DoctrineHandler::class], $handler2],
                ]
            );

        $handler1
            ->expects(self::atLeastOnce())
            ->method('createCondition')
            ->with(self::identicalTo($spec1), self::identicalTo($this->queryBuilder))
            ->willReturn('spec1');

        $handler2
            ->expects(self::atLeastOnce())
            ->method('createCondition')
            ->with(self::identicalTo($spec2), self::identicalTo($this->queryBuilder))
            ->willReturn('spec2');

        $expr = $this->handler->createCondition(
            new AllOf($spec1, $spec2),
            $this->queryBuilder
        );

        self::assertEquals('spec1 AND spec2', (string) $expr);
    }

    /**
     * Проверяет возврат обработчиком правильного имени спецификации.
     *
     * @throws \Throwable
     */
    public function testReturnValidSpecificationName(): void
    {
        self::assertEquals(AllOf::class, $this->handler->getSpecificationClassName());
    }

    /**
     * Проверяет вбрасывание исключения при передаче неправильной спецификации.
     *
     * @throws \Throwable
     */
    public function testThrowExceptionOnInvalidSpecification(): void
    {
        $this->expectException(UnsupportedSpecificationException::class);
        $this->expectExceptionMessageMatches(
            '/.*AllOfHandler supports only .*AllOf specifications, but Mock_Specification_\w+ given./'
        );

        $this->handler->createCondition(
            $this->createMock(Specification::class),
            $this->queryBuilder
        );
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
        $this->queryBuilder = $this->createConfiguredMock(
            QueryBuilder::class,
            ['expr' => new Expr()]
        );

        $this->handler = new AllOfHandler($this->handlerRegistry);
    }
}
