<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Tests\Unit\Handler;

use DobroSite\Specification\Any;
use DobroSite\Specification\Doctrine\Handler\AnyHandler;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Тесты обработчика спецификаций «Any».
 *
 * @covers \DobroSite\Specification\Doctrine\Handler\AnyHandler
 */
final class AnyHandlerTest extends TestCase
{
    /**
     * Проверяемый обработчик.
     */
    private AnyHandler $handler;

    /**
     * Составитель запросов.
     *
     * @var QueryBuilder&MockObject
     */
    private QueryBuilder $queryBuilder;

    /**
     * Проверяет создание выражения из спецификации.
     *
     * @throws \Throwable
     */
    public function testCreateExpression(): void
    {
        $expr = $this->handler->createCondition(
            new Any(),
            $this->queryBuilder
        );

        self::assertEquals('1 = 1', (string) $expr);
    }

    /**
     * Проверяет возврат обработчиком правильного имени спецификации.
     *
     * @throws \Throwable
     */
    public function testReturnValidSpecificationName(): void
    {
        self::assertEquals(Any::class, $this->handler->getSpecificationClassName());
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
            '/.*AnyHandler supports only .*Any specifications, but Mock_Specification_\w+ given./'
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

        $this->queryBuilder = $this->createConfiguredMock(
            QueryBuilder::class,
            ['expr' => new Expr()]
        );

        $this->handler = new AnyHandler();
    }
}
