<?php

namespace DobroSite\Specification\Doctrine;

use DobroSite\Specification\Doctrine\Exception\UnsupportedSpecificationException;
use DobroSite\Specification\Doctrine\Handler\Handler;
use DobroSite\Specification\Specification;

/**
 * Реестр обработчиков спецификаций.
 *
 * @since 1.0
 */
interface HandlerRegistry
{
    /**
     * Возвращает обработчик для указанной спецификации.
     *
     * @param Specification $specification
     *
     * @return Handler
     *
     * @throws UnsupportedSpecificationException Если переданная спецификация не поддерживается.
     *
     * @since 1.0
     */
    public function getHandlerFor(Specification $specification);
}
