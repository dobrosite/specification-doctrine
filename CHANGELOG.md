# История изменений

Формат этого файла соответствует рекомендациям [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/).
Проект использует [семантическое версионирование](http://semver.org/spec/v2.0.0.html).

## Не выпущено

### Добавлено

- Добавлен метод `QueryBuilder::createParameter`. 


## 1.0.0 - 09.10.2019

### Удалено

- Исключение `UnsupportedSpecificationException`.
- Класс `HandlerRegistry`. Его функции переложены на `Bridge``.

### Изменено

- Минимальная версия PHP теперь 7.1.
- Минимальная версия `dobrosite/specification` теперь 2.0.
- Интерфейс `Handler` переименован в `DoctrineHandler` и унаследован от `Handler` из
  `dobrosite/specification`.


## 0.2.0 - 22.05.2019

### Изменено

- `QueryBuilder::match` теперь не замещает, а добавляет условие `WHERE`.


## 0.1.0 - 06.05.2019

Первая версия.
