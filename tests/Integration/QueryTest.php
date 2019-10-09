<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Tests\Integration;

use DobroSite\Specification\Doctrine\Bridge;
use DobroSite\Specification\Doctrine\Examples\Domain\Entity\Product;
use DobroSite\Specification\Doctrine\Examples\Domain\Specification\Article;
use DobroSite\Specification\Doctrine\Examples\Infrastructure\Doctrine\Specification\ArticleHandler;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

/**
 * Тесты с использованием реальной БД.
 *
 * @coversNothing
 */
class QueryTest extends TestCase
{
    /**
     * Менеджер сущностей.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TODO Дать краткое описание метода.
     */
    public function testSpecificationSatisfied(): void
    {
        $bridge = new Bridge();
        $bridge->registerHandler(new ArticleHandler());

        $specification = new Article('А1');

        $queryBuilder = $bridge->createQueryBuilder($this->entityManager);
        $queryBuilder
            ->from(Product::class, 'p')
            ->select('p')
            ->match($specification);


        $result = $queryBuilder->getQuery()->getResult();

        self::assertCount(1, $result);
        self::assertEquals('А1', $result[0]->article());
    }

    /**
     * Готовит окружение теста.
     *
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $connection = DriverManager::getConnection(['url' => 'sqlite://:memory:']);

        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . '/../../examples/Domain/Entity'],
            true
        );

        $this->entityManager = EntityManager::create($connection, $config);

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaSql = $schemaTool->getUpdateSchemaSql($schemaData);
        foreach ($schemaSql as $query) {
            $this->entityManager->getConnection()->executeQuery($query);
        }

        $product = new Product('А1');
        $this->entityManager->persist($product);

        $product = new Product('А2');
        $this->entityManager->persist($product);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
