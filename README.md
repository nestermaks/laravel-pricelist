# Multilingual pricelist package for Laravel with migrations and API controllers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nestermaks/laravel-pricelist.svg?style=flat-square)](https://packagist.org/packages/nestermaks/laravel-pricelist)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/nestermaks/laravel-pricelist/run-tests?label=tests)](https://github.com/nestermaks/laravel-pricelist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/nestermaks/laravel-pricelist/Check%20&%20fix%20styling?label=code%20style)](https://github.com/nestermaks/laravel-pricelist/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/nestermaks/laravel-pricelist.svg?style=flat-square)](https://packagist.org/packages/nestermaks/laravel-pricelist)

This package allows you to manage your pricelists and reorder items inside. It's not a kind of pricing tables, where you have a list of features and a price below. It is responsible for creating lists, where every item has its own price. Also every item can belong to many pricelists.

This package includes:
1. Models
2. Migrations
3. Api routes
4. Api controllers
5. Translations
6. Config file


## Installation

You can install the package via composer:

```bash
composer require nestermaks/laravel-pricelist
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Nestermaks\LaravelPricelist\LaravelPricelistServiceProvider" --tag="laravel-pricelist-migrations"
php artisan migrate
```

## Configuration
You can override the default options for used locales, api route prefixes and validation rules. First publish the configuration:
```bash
php artisan vendor:publish --provider="Nestermaks\LaravelPricelist\LaravelPricelistServiceProvider" --tag="laravel-pricelist-config"
```

This is the contents of the published config file:

```php
<?php
// config for Laravel Pricelist
return [

    /////////////////////////////////////////////
    //Locales for Astrotomic/laravel-translatable
    /////////////////////////////////////////////
    'locales' => [
        'en',
        'ru',
        'uk',
    ],


    ///////////////////
    //API routes prefix
    ///////////////////
    'api' => 'nestermaks-api',


    //////////////////////////
    //Pricelists routes prefix
    //////////////////////////
    'pricelists' => 'pricelists',


    ///////////////////////////////
    //Pricelist items routes prefix
    ///////////////////////////////
    'items' => 'pricelist-items',


    ////////////////////////////////////////////////
    //Items amount shown when index method is called
    ////////////////////////////////////////////////
    'pricelists-per-page' => 10,
    'pricelist-items-per-page' => 10,


    ///////////////////////////////////////////////
    //Validation rules for store and update methods
    ///////////////////////////////////////////////
    'store-pricelists' => [
        'title' => ['required', 'max:256'],
        'description' => ['max:1000'],
        'lang' => ['max:16'],
        'order' => ['numeric', 'min:0', 'max:65535'],
        'active' => ['boolean']
    ],

    'update-pricelists' => [
        'title' => ['max:256'],
        'description' => ['max:1000'],
        'lang' => ['max:16'],
        'order' => ['numeric', 'min:0', 'max:65535'],
        'active' => ['boolean']
    ],

    'store-pricelist-items' => [
        'title' => ['required', 'max:256'],
        'units' => ['required', 'max:256'],
        'lang' => ['max:16'],
        'shortcut' => ['required', 'max:256'],
        'price' => ['required', 'numeric', 'min:0'],
        'max_price' => ['prohibited_if:price_from,true'],
        'price_from' => ['boolean'],
        'active' => ['boolean']
    ],

    'update-pricelist-items' => [
        'title' => ['max:256'],
        'units' => ['max:256'],
        'lang' => ['max:16'],
        'shortcut' => ['max:256'],
        'price' => ['numeric', 'min:0'],
        'max_price' => ['prohibited_if:price_from,true'],
        'price_from' => ['boolean'],
        'active' => ['boolean']
    ],


];

```

## Database
Migrations files are provided with such tables:
1. **pricelists**. This table is like a container for pricelist items. It has such fields:
    1. 'order' - sets priority on a frontend
    2. 'active' - defines if the pricelist is active or not
    
    > Translatable fields of the table 'pricelists' are in the table 'pricelist_translations'. Table 'pricelists' has many pricelist translations
    
2. **pricelist_translations**. Fields:
    1. 'pricelist_id' - id of the related pricelist
    2. 'locale' - language of translation
    3. 'title' - title of the pricelist
    4. 'description' - description of the pricelist if needed
    
---
3. **pricelist_items**. Elements of pricelists. Fields:
    1. 'shortcut' - for internal usage to identify your pricelist item. For example, if you have different items with the same title/
    2. 'price' - price of your product. In case the 'max_price' field is set, this one stands for minimum price.
    3. 'max_price' - if your product has no fixed price this field describes maximum price.
    4. 'price_from' - if the price of your product starts from the value set in 'price' field and has no upper bound. Boolean.
    5. 'active' - defines if the pricelist item is active or not
    
    > Translatable fields of the table 'pricelist_items' are in the table 'pricelist_items_translations'. Table 'pricelists' has many pricelist translations
   

4. **pricelist_items_translations**. Fields:
    1. 'pricelist_item_id' - id of the related pricelist item
    2. 'locale' - language of translation
    3. 'title' - name of your product
    4. 'units' - in what units your product can be calculated
    
---
5. **pricelist_pricelist_item**. Pivot table for pricelists and pricelist_items in many to many relationship. Also contains field 'item_order', which defines order of an element within the table.

6. **pricelistables**. Polymorphic many to many relationship table. Responsible for attaching a pricelist to your pricelistable models.

## Usage

### Registering Models
To let your models be able to attach pricelists, add the HasPricelist trait to your model class

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nestermaks\LaravelPricelist\HasPricelist;

class Offer extends Model
{
    use HasPricelist;
    ...
}
```

### Associating pricelists
You can associate a pricelist with a model like this:

```php
$offer = Offer::first();
$pricelist = Pricelist::first();

$offer->addPricelist($pricelist);
```

Or you can detach pricelist from a model:
```php
$offer->removePricelist($pricelist);
```
To watch all pricelists of the model:
```php
$offer->pricelists();
```

### Show pricelists and pricelist items
To show all active pricelists:
```php
Pricelist::getActiveItems();
```
The same for pricelist items
```php
PricelistItem::getActiveItems();
```
To show pricelist items related to the pricelist:
```php
Pricelist::relatedItems();
```
To show pricelists where the pricelist item is present:
```php
PricelistItem::relatedItems();
```
---
### Attach and detach pricelist items to pricelist
Assuming we have such variables:
```php
$pricelists = Pricelist::all();
$pricelist = Pricelist::first();
$pricelist_items = PricelistItem::all();
$pricelist_item = PricelistItem::first();
```
To attach items to the pricelist:
```php
$pricelist->attachItems($pricelist_items);
```
Or to attach one pricelist item to many pricelists:

```php
$pricelist_item->attachItems($pricelists);
```

> Notice that the argument in both cases is a Collection.

### Reordering pricelist items within the pricelist

When you attach pricelist items to the pricelist, each of them gets its order number. To see order number of a pricelist item in the pricelist use:
```php
$pricelist->getItemOrder($pricelist_item);
```
or
```php
$pricelist_item->getItemOrder($pricelist);
```
To set a new order number of a pricelist item within the pricelist use:
```php
$pricelist->changeItemOrder($pricelist_item, 3);
```
or
```php
$pricelist_item->changeItemOrder($pricelist, 3);
```
>Second argument is for new order number. After you assign a new order number of the pricelist item, another ones will rearrange appropriately inside the pricelist.

## Api routes

You may use api calls to manage your pricelists. Set desired api prefixes in a config file or leave it default. For pricelists and pricelists items CRUDS are used api resource routes with api resource controllers. You may look through the Laravel [DOCS](https://laravel.com/docs/8.x/controllers#api-resource-routes).

### CRUDS

#### Pricelists

Parameters to use in CRUDS:
   1. title
   2. description
   3. lang
   4. order
   5. active

For example, to update a pricelist with id "12" make a patch request:
```
https://yoursite.com/nestermaks-api/pricelists/12?active=0&title=web site development&description=here goes description&lang=en&order=10
```

#### Pricelist items

Parameters to use in CRUDS:
   1. title
   2. units
   3. lang
   4. shortcut
   5. price
   6. max_price
   7. price_from
   8. active
      
For example, to update a pricelist with id "12" item make a patch request:
```
https://yoursite.com/nestermaks-api/pricelist-items/12?title=landing page development&units=hour&lang=en&shortcut=land-dev&price=10&max-price=15&price-from=0&active=1
```

### Attach and detach pricelists from pricelist items

URL should look like:

```
https://yoursite.com/nestermaks-api/pricelists/relation/{$action}
```

$action can be 'detachItems' of 'AttachItems'

Parameters:
   1. pricelist_id
   2. pricelist_items_id

One of them should be integer while another one should be an array.

Example:
```
https://yoursite.com/nestermaks-api/pricelists/relation/attachItems?pricelist_id=2&pricelist_item_id=[3,4,5,6]
```



### Associate pricelist with your model

URL should look like

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [nestermaks](https://github.com/nestermaks)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
