# Оценка сроков

Решение будет в виде репозитория с исходным кодом и конфигурацией vagrant, который создаст виртуальную 
машину в VirtualBox, настроит все компоненты (PHP, web-server, DB...). Результат работы (API & GUI отчета)
будет доступен по HTTP локально.

## Реализация API

1. Настройка среды разработки.

Создание git репозитория, настройка vagrant и конфигурации гостевой машины, настройка будущего API: 
PHPUNIT тестов, автолоадера, менеджера пакетов. 

~ 3 часа

2. Проектирование структуры базы данных. Создание схемы, и размещение в виртуальной машине.

~ 2 часа

3. Проектирование API: resources URI, methods, parameters. Объявление JSON схем для описания и валидации типов данных.

~ 2 часа

4. Реализация API ресурса для создания клиента.

~ 1 час

5. Реализация API ресурса для зачисления денег на кошелек клиента

~ 1 час

6. Реализация API ресурса для перевода денег на другой кошелек

~ 1 час

7. Реализация API ресурса для указания курсов валют на дату

~ 1 час

8. Реализация API ресурса для постраничного вывода списка транзакций

~ 1 час

9. Реализация API ресурса для подсчёта общей суммы операций по счету за период

~ 1 час

10. Реализация API ресурса для выгрузки отчета в файл

~ 1 час

## Реализация пользовательского интерфейса отчета

1. Настройка среды разработки: менеджер пакетов + webpack. Понадобятся: datepicker для выбора дат отчета, paginator для
постраничного вывода, fetch для запросов в API, bootstrap для приятного внешнего вида.  

~ 3 часа

2. Сборка интерфейса из компонентов

~ 8 часов

## Документация

Небольшое описание в README.md репозитория

~ 1 час

Итого: около 26 часов.
