<?php

namespace DobroSite\Specification\Doctrine\Exception;

use DobroSite\Specification\Doctrine\Handler\Handler;
use DobroSite\Specification\Specification;

/**
 * Спецификация не поддерживается обработчиком.
 */
class UnsupportedSpecificationException extends \LogicException
{
    /**
     * Создаёт исключение.
     *
     * @param Specification $specification Обрабатываемая спецификация.
     * @param Handler|null  $handler       Обрабатывающая фабрика.
     */
    public function __construct(Specification $specification, Handler $handler = null)
    {
        if ($handler !== null) {
            $message = sprintf(
                '%s supports only %s specifications, but %s given.',
                get_class($handler),
                $handler->getSpecificationClassName(),
                get_class($specification)
            );
        } else {
            $message = sprintf(
                '%s specification not supported by Doctrine bridge.',
                get_class($specification)
            );
        }

        parent::__construct($message);
    }
}
