<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Examples\Domain\Specification;

use DobroSite\Specification\Doctrine\Examples\Domain\Entity\Product;
use DobroSite\Specification\Specification;

/**
 * Артикул.
 */
class Article implements Specification
{
    /**
     * Значение артикула.
     *
     * @var string
     */
    private $article;

    /**
     * Создаёт спецификацию
     *
     * @param string $article Значение артикула.
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Возвращает ожидаемое значение артикула.
     *
     * @return string
     */
    public function getArticle(): string
    {
        return $this->article;
    }

    /**
     * Возвращает true, если переданный кандидат удовлетворяет спецификации.
     *
     * @param mixed $candidate
     *
     * @return bool
     */
    public function isSatisfiedBy($candidate): bool
    {
        if (!$candidate instanceof Product) {
            return false;
        }

        return $candidate->article() === $this->article;
    }
}
