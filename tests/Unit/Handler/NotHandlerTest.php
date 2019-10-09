<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Tests\Unit\Handler;

use DobroSite\Specification\Doctrine\Handler\DoctrineHandler;
use DobroSite\Specification\Doctrine\Handler\NotHandler;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Handler\HandlerRegistry;
use DobroSite\Specification\Logical\Not;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Тесты обработчика спецификаций «Not».
 *
 * @covers \DobroSite\Specification\Doctrine\Handler\NotHandler
 */
class NotHandlerTest extends TestCase
{
    /**
     * Проверяемый обработчик.
     *
     * @var NotHandler
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
        $spec = $this->createConfiguredMock(Specification::class, []);

        $handler = $this->createMock(DoctrineHandler::class);

        $this->handlerRegistry
            ->expects(self::once())
            ->method('getHandlerFor')
            ->with(
                self::identicalTo($spec),
                self::equalTo([DoctrineHandler::class])
            )
            ->willReturn($handler);

        $handler
            ->expects(self::atLeastOnce())
            ->method('createCondition')
            ->with(self::identicalTo($spec), self::identicalTo($this->queryBuilder))
            ->willReturn('spec');

        $expr = $this->handler->createCondition(new Not($spec), $this->queryBuilder);

        self::assertEquals('NOT(spec)', (string) $expr);
    }

    /**
     * Проверяет возврат обработчиком правильного имени спецификации.
     *
     * @throws \Throwable
     */
    public function testReturnValidSpecificationName(): void
    {
        self::assertEquals(Not::class, $this->handler->getSpecificationClassName());
    }

    /**
     * Проверяет вбрасывание исключения при передаче неправильной спецификации.
     *
     * @throws \Throwable
     */
    public function testThrowExceptionOnInvalidSpecification(): void
    {
        $this->expectException(UnsupportedSpecificationException::class);
        $this->expectExceptionMessageRegExp(
            '/.*NotHandler supports only .*Not specifications, but Mock_Specification_\w+ given./'
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

        $this->handler = new NotHandler($this->handlerRegistry);
    }
}
