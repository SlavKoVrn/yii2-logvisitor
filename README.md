# Log site requests component and draw charts of these requests module for Yii 2.0 Framework

The Yii2 extension uses visualize.jQuery.js to draw progress charts of site requests logged by calendar dates.

[Log visitor demo page](http://yii2.kadastrcard.ru/logvisitor).

![Log visitor](http://yii2.kadastrcard.ru/uploads/logvisitor.jpg)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run:

```bash
composer require slavkovrn/yii2-logvisitor
```

or add

```bash
"slavkovrn/yii2-logvisitor": "*"
```

to the require section of your `composer.json` file.

Usage
-----

**To make LogVisitorComponent log site requests**

* 1. add link to component in your config (in my case it's /config/web.php)

```php
return [
    'components' => [
        'logvisitor' => [
            'class' => 'slavkovrn\logvisitor\LogVisitorComponent'
            'filterIp' => '127.0.0.1,213.87.',  /* comma separated substrings of IP  to be filtered of log in table , begining from first position  */
            'filterUri' => '/,debug',           /* comma separated substrings of URI to be filtered of log in table */
        ],
    ],
];
```

* 2. add link to log site requests automatically

```php
return [
	'bootstrap' => ['log', 'logvisitor'],
]; 
```

**To draw charts of requests progress**

* 3. add link to LogVisitorModule in your config

```php
return [
    'modules' => [
        'logvisitor' => [
            'class' => 'slavkovrn\logvisitor\LogVisitorModule',
        ],
    ],
]; 
```

and now you can see the charts of site requests in progress by calendar dates via http://yoursite.com/logvisitor url

<a href="mailto:slavko.chita@gmail.com">write comments to admin</a>
