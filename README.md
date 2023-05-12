# Курсы валют ЦБ России

**Библиотека для получения и парсинга XML-данных ЦБ России о курсах валют.**

## Доступные ресурсы

* Справочник валют
* Котировки на заданный день
* Динамика котировок за период времени

**Вывод данных:** XML или PHP-массив.

## Установка
### Composer
```sh
composer require webdenis77/cbr:^2.0
```

### Обычная
Скачайте и распакуйте архив с библиотекой в нужное вам место, затем подключите автолоадер библиотеки:
```php
require('path-to-package/autoloader.php');
```

## Использование

### Справочник валют

[http://www.cbr.ru/scripts/XML_valFull.asp](http://www.cbr.ru/scripts/XML_valFull.asp)

```php
<?php

try {
    
    $resource = new \CBR\Catalog();
    
    $result = $resource
        // Опционально, фильтр по типам валют
        ->setType(\CBR\Catalog::TYPE_DAILY) // Валюты, устанавливаемые ежедневно (по умолчанию)
        ->setType(\CBR\Catalog::TYPE_MONTHLY) // Валюты, устанавливаемые ежемесячно
        
        // Выполнение запроса 
        ->request()
        
        // Получение необработанного ответа
        ->getResultXML();
        
        // Получение обработанного ответа
        ->getResult();

} catch (\Exception $e) {
    // Обработка исключения
}
```

### Котировки на заданный день

[http://www.cbr.ru/scripts/XML_daily.asp](http://www.cbr.ru/scripts/XML_daily.asp)

```php
<?php

try {
    
    $resource = new \CBR\CurrencyDaily();
    
    $result = $resource
        // Опционально, дата в становленном формате
        ->setDateFormat('d/m/Y')
        ->setDate('20/07/2015')
        
        ->setDateFormat('Y-m-d')
        ->setDate('2015-07-20')
        
        // Опционально, фильтр по кодам валют в установленном формате
        ->setKeyFormat(\CBR\Resource::KEY_CHAR)
        ->setCurrencies(['USD', 'EUR'])
        
        ->setKeyFormat(\CBR\Resource::KEY_NUM)
        ->setCurrencies(['840', '978'])
        
        ->setKeyFormat(\CBR\Resource::KEY_ID)
        ->setCurrencies(['R01235', 'R01239'])
        
        // Выполнение запроса
        ->request()
        
        // Получение необработанного ответа
        ->getResultXML();
        
        // Получение обработанного ответа
        ->getResult();
        /* Вернется именованный массив
        Ключи - коды валют, установленные через ->setKeyFormat()
        По умолчанию - \CBR\Resource::KEY_CHAR */

    /* Дата обновления ставок в результате может не совпадать с той, которую вы указали
    (по выходным дням, например, ставки не обновляются).
    Актуальная дата, по которой вы получили информацию
    сохраняется в обработчике после вызова getResult() и ее можно получить так: */
    $result_date = $resource->getResultDate();
} catch (\Exception $e) {
    // Обработка исключения
}
```

### Динамика котировок за период времени

```php
<?php

try {
    $resource = new \CBR\CurrencyPeriod();
    
    $result = $resource
        // Даты "c" и "по" в установленном формате
        ->setDateFormat('d/m/Y')
    	->setInterval('20/06/2015', '20/07/2015')
    	
        ->setDateFormat('Y-m-d')
        ->setInterval('2015-06-20', '2015-07-20')
    	
    	// Код валюты в формате Банка России 
    	->setCurrency('R01535')
    	
    	// Выполнение запроса
    	->request()
    	
    	// Получение необработанного ответа
    	->getResultXML();
    	
    	// Получение обработанного ответа
    	->getResult();
        /* Вернется именованный массив
        Ключи - дата в формате, установленном через >setDateFormat() */
   
} catch (\Exception $e) {
    // Обработка исключения
}
```