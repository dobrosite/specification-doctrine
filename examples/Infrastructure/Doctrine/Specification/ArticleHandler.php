<?php

namespace DobroSite\Specification\Doctrine\Examples\Infrastructure\Doctrine\Specification;

use DobroSite\Specification\Doctrine\Examples\Domain\Entity\Product;
use DobroSite\Specification\Doctrine\Exception\UnsupportedSpecificationException;
use DobroSite\Specification\Doctrine\Handler\Handler;
use DobroSite\Specification\Doctrine\Examples\Domain\Specification\Article;
use DobroSite\Specification\Doctrine\QueryBuilder\QueryBuilder;
use DobroSite\Specification\Specification;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Comparison;

/**
 * Обработчик спецификации Article.
 */
class ArticleHandler implements Handler
{
    /**
     * Создаёт условие на основе спецификации.
     *
     * @param Specification $specification Спецификация.
     * @param QueryBuilder  $queryBuilder  Построитель запросов.
     *
     * @return Base|Comparison|string
     *
     * @throws UnsupportedSpecificationException Если переданная спецификация не поддерживается.
     *
     * @since 1.0
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
    public function getSpecificationClassName()
    {
        return Article::class;
    }
}
