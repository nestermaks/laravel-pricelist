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
