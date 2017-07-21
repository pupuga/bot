# Custom Bot for WordPress

* [Requirements](#requirements)
* [Installation](#installation)
* [Custom short codes](#custom-short-codes)
* [Multi lang](#multi-lang)

## Requirements
* Carbon fields >= 1.6

## Installation
* Put files into theme directory. You must put this code into *function.php*
```php
require_once __DIR__. '/pupuga-framework/modules/InitModules.php';
```

## Custom short codes
* If you want to use **short codes**, you must put this code into *function.php*
And create method into ShortCode class. This method gets function in inserting code
```php

function test($values) 
{
    return 'This is test short code function!!!!!' . $values[0]->value;
}
```

## Multi lang 
* You must put this code into *function.php* first. Where **$lang** is current lang
```php
function useBotLang() 
{
    return $lang;
}
```