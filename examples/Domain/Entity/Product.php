<?php

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
     * @Column(type="string")
     * @Id
     */
    private $article;

    /**
     * Создаёт продукт.
     *
     * @param string $article Артикул.
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Артикул.
     *
     * @return string
     */
    public function article()
    {
        return $this->article;
    }
}
