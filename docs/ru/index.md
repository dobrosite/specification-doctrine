# Поддержка спецификаций для Doctrine

Эта библиотека позволяет использовать
[dobrosite/specification](https://github.com/dobrosite/specification/) совместно с
[Doctrine ORM](https://www.doctrine-project.org/projects/orm.html).

## Принцип работы

1. Для каждой спецификации надо написать обработчик с интерфейсом
   [Specification\Doctrine\Handler\DoctrineHandler], который будет создавать для неё выражение на
   DQL.
2. Создать экземпляр [Specification\Doctrine\Bridge] и зарегистрировать в нём все обработчики.
3. Создать экземпляр [Specification\Doctrine\QueryBuilder\QueryBuilder], передав в него используемый
   экземпляр `EntityManager` и созданный ранее экземпляр [Specification\Doctrine\Bridge].  
4. Полученный экземпляр [Specification\Doctrine\QueryBuilder\QueryBuilder] можно использовать для
   конструирования запросов на DQL, применяя спецификации с помощью метода `match`.
  
В последующих разделах все действия разбираются подробнее.

## Создание обработчиков

Обработчик спецификации — это класс с интерфейсом [Specification\Doctrine\Handler\DoctrineHandler],
задачей которого является трансляция спецификации в DQL.

- [Пример обработчика](../../examples/Infrastructure/Doctrine/Specification/ArticleHandler.php)

Обработчик должен предоставлять следующие методы.

### getSpecificationClassName

Метод должен всего лишь возвращать имя класса поддерживаемых спецификаций. Пример:

```php
public function getSpecificationClassName(): string
{
    return Article::class;
}
```

### createCondition

Это самый главный метод, который собственно и превращает спецификацию в выражение на DQL. На входе 
он должен принимать спецификацию и экземпляр [Specification\Doctrine\QueryBuilder\QueryBuilder].

- В начале следует проверить, поддерживается ли класс переданной спецификации этим разработчиком.
  Если нет, то вбросить исключение `UnsupportedSpecificationException`.
- В конце следует вернуть выражение, созданное с помощью `QueryBuilder::expr()`.

Пример:

```php
public function createCondition(Specification $specification, QueryBuilder $queryBuilder)
{
    if (!$specification instanceof Article) {
        throw new UnsupportedSpecificationException($specification, $this);
    }

    $product = $queryBuilder->getAliasFor(Product::class);
    $article = $queryBuilder->createParameter('article', $specification->getArticle());

    return $queryBuilder->expr()->eq($product . '.article', $article);
}
```

Важные моменты.

Если у спецификации есть параметры, их следует добавлять в `QueryBuilder` при помощи метода
`createParameter`, как показано в примере. Он обеспечит правильную привязку значений при
многократном использовании спецификации в одном запросе.

Для получения псевдонима класса сущностей используйте метод `QueryBuilder::getAliasFor`, который
при наличии ранее заданного псевдонима вернёт его, а при отсутствии, создаст новый.

## Bridge

Класс `Bridge` выполняет роль центрального узла, связывающего вместе все объекты, необходимые для
использования спецификаций с Doctrine. Рекомендуется создавать и настраивать его экземпляр с
помощью используемого в проекте контейнера зависимостей.

Все созданные обработчики следует зарегистрировать в экземпляре `Bridge`.

Пример:

```php
<?php

use DobroSite\Specification\Doctrine\Bridge;
use Infrastructure\Doctrine\Specification\Handler\ArticleHandler;

$bridge = new Bridge();

$bridge->registerHandler(new ArticleHandler());
// Другие обработчики.
``` 

Пример для Symfony:

```yaml
services:

  # Регистрируем в контейнере все обработчики спецификаций.
  Infrastructure\Doctrine\Specification\Handler\:
    resource: '%kernel.project_dir%/Infrastructure/Doctrine/Specification/Handler'
  
  # Регистрируем Bridge. Обратите внимание на имя, под которым его регистрируем!
  DobroSite\Specification\Doctrine\QueryBuilderFactory:
    class: DobroSite\Specification\Doctrine\Bridge
    # Добавляем обработчики:
    calls:
      - ['registerHandler', ['@Infrastructure\Doctrine\Specification\ArticleHandler']]
      # Другие обработчики.
```

Обратите внимание, что экземпляр `Bridge` в этом случае должен регистрироваться под именем своего
интерфейса — [Specification\Doctrine\QueryBuilderFactory]. Это нужно для правильной работы
автоматического связывания.

## Применение спецификаций

Рассмотрим применение спецификаций на примере следующего хранилища:

```php
<?php
namespace Infrastructure\Doctrine\Repository;

use DobroSite\Specification\Doctrine\QueryBuilderFactory;
use DobroSite\Specification\Specification;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Entity\Foo;
use Domain\Repository\FooRepository;

class DoctrineFooRepository implements FooRepository
{
    /**
     * Фабрика составителей запросов.
     *
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        QueryBuilderFactory $queryBuilderFactory
    ) {
        $this->entityManager = $entityManager;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }
    
    /**
     * Ищет сущности, удовлетворяющие переданной спецификации.
     *
     * @param Specification $specification
     *
     * @return Foo[]
     */
    public function find(Specification $specification): array
        {
        $queryBuilder = $this->queryBuilderFactory->createQueryBuilder($this->entityManager);
        $queryBuilder
            ->from(Foo::class, 'f')
            ->select('f')
            ->match($specification);
        
        return $queryBuilder->getQuery()->getResult();
    }
}
```

Хранилищу требуется класс с интерфейсом [Specification\Doctrine\QueryBuilderFactory], позволяющим
создавать составители запросов, поддерживающие спецификации. Таким классом является
[Specification\Doctrine\Bridge].

Составление и выполнение запросов производится как обычно с Doctrine. А для применения спецификаций
следует использовать метод `match`. 


[Specification\Doctrine\Bridge]: ../../src/Bridge.php
[Specification\Doctrine\Handler\DoctrineHandler]: ../../src/Handler/DoctrineHandler.php
[Specification\Doctrine\QueryBuilder\QueryBuilder]: ../../src/QueryBuilder/QueryBuilder.php
[Specification\Doctrine\QueryBuilderFactory]: ../../src/QueryBuilderFactory.php
