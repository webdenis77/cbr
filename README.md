# Курсы валют ЦБ России
**Библиотека для получения и парсинга XML-данных о курсах валют**

## Доступные ресурсы
**Котировки на заданный день**  
[http://www.cbr.ru/scripts/XML_daily.asp](http://www.cbr.ru/scripts/XML_daily.asp)  
Возможно получение как полного списка, так и фильтрация по выбранным кодам валют (USD, EUR и т.д.).  

**Динамика котировок выбранной валюты**  
[http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=10/07/2015&date_req2=20/07/2015&VAL_NM_RQ=R01235](http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=10/07/2015&date_req2=20/07/2015&VAL_NM_RQ=R01235)

**Вывод данных:** XML или PHP-массив.

## Установка
### Composer
Добавьте в блок "require" в composer.json вашего проекта:
```json
"nabarabane/cbr": "~1.0"
```
или в командной строке:
```sh
composer require nabarabane/cbr:~1.0
```

### Обычная
Скачайте и распакуйте архив с файлами в нужное вам место, затем подключите автолоадер библиотеки:
```php
require('Autoloader.php');
```

## Использование
### Котировки на заданный день
[http://www.cbr.ru/scripts/XML_daily.asp](http://www.cbr.ru/scripts/XML_daily.asp)  
```php
<?php

use CBR\CurrencyDaily;

try {
    $handler = new CurrencyDaily();
    $result = $handler
        ->setDate('20/07/2015') // Опционально, дата в формате "d/m/Y"
        ->setCodes(['USD', 'EUR']) // Опционально, фильтр по кодам валют
        //->setCodes(['840', '978']) Или можно так
        ->request() // Выполнение запроса
        ->getResult();
    /* Вернется именованный массив
    ->getResult() - ключи по умолчанию: буквенные коды валют (USD, EUR)
    ->getResult(CurrencyDaily::KEY_NUM) - ключи: цифровые коды валют (840, 978)
    ->getResult(CurrencyDaily::KEY_ID) - ключи: уникальные ID валют в формате Банка России,
    используются для получения динамики котировок по валюте за период времени */

    /* Дата обновления ставок в результате может не совпадать с той, которую вы указали (по выходным дням,
    например, ставки не обновляются). Актуальная дата, по которой вы получили информацию
    сохраняется в хендлере после вызова getResult() и ее можно получить так */
    $date = $handler->getResultDate('Y-m-d'); // Формат по умолчанию - 'Y-m-d'

    /* Вывод в XML
    Возвращает оригинальный XML, полученный с сервера, игнорирует фильтр по кодам валют.
    Я подразумеваю, если вам нужен XML на выходе, то вы отлично знаете, что с ним делать */
    ->getResultXML();
} catch (\Exception $e) {
	echo $e->getMessage();
}
```

### Динамика котировок за период времени
Справочник кодов валют формата Банка России - [http://www.cbr.ru/scripts/XML_val.asp](http://www.cbr.ru/scripts/XML_val.asp)
```php
<?php

use CBR\CurrencyPeriod;

try {
    $result = (new CurrencyPeriod())
    	->setDateFrom('20/06/2015') // Дата "c" в формате "d/m/Y"
    	->setDateTo('20/07/2015') // Дата "по" в формате "d/m/Y"
    	->setCurrency('R01535') // Код валюты в формате Банка России
    	->request() // Выполнение запроса
    	->getResult();
    /* Вернется именованный массив.
    Ключи по умолчанию - дата в формате 'Y-m-d'
    Нужный формат можно передать в метод параметром
    ->getResult('Y/m/d')

    /* Вывод в XML */
    ->getResultXML();
} catch (\Exception $e) {
	echo $e->getMessage();
}
```