<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Examples\Infrastructure\Doctrine\Specification;

use DobroSite\Specification\Doctrine\Examples\Domain\Entity\Product;
use DobroSite\Specification\Doctrine\Handler\DoctrineHandler;
use DobroSite\Specification\Doctrine\Examples\Domain\Specification\Article;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Exception\Handler\UnsupportedSpecificationException;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Обработчик спецификации Article.
 */
class ArticleHandler implements DoctrineHandler
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
        if (!$specification instanceof Article) {
            throw new UnsupportedSpecificationException($specification, $this);
        }

        $product = $queryBuilder->getAliasFor(Product::class);
        $queryBuilder->setParameter('article', $specification->getArticle());

        return $queryBuilder->expr()->eq($product . '.article', ':article');
    }

    /**
     * Возвращает имя класса поддерживаемых спецификаций.
     *
     * @return string
     */
    public function getSpecificationClassName(): string
    {
        return Article::class;
    }
}
