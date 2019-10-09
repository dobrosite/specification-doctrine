<?php

declare(strict_types=1);

namespace DobroSite\Specification\Doctrine\Examples\Domain\Entity;

/**
 * Продукт.
 *
 * @Entity
 */
class Product
{
    /**
     * Артикул.
     *
     * @var string
     *
     * @Id
     * @Column(type="string")
     */
    private $article;

    /**
     * Создаёт продукт.
     *
     * @param string $article Артикул.
     */
    public function __construct(string $article)
    {
        $this->article = $article;
    }

    /**
     * Артикул.
     *
     * @return string
     */
    public function article(): string
    {
        return $this->article;
    }
}
