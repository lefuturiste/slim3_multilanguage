# Slim framework 3 Multilanguage

Multilanguage middleware class for slim framework 3 

-------------------------------------------------

### Dependecies

- PHP >= 5.3
- Slim Framework =~ 3.7

-------------------------------------------------

## 1. Installing

Easy install via composer. Still no idea what composer is? Inform yourself [here](http://getcomposer.org).

```composer require lefuturiste/slim3_multilanguage```

-------------------------------------------------

## 2. How to it work ?

- It is a slim3 middleware
- It can use Twig (no require)
- It use external class : STAILang (STAN-TAb Corp.) for have array of all translates
- It create lang directory, in, json file with all translates
- It create container 'lang' for store array of all translates

-------------------------------------------------

## 3. Usage example

```php
<?php
//Application router
require '../vendor/autoload.php';

$app = new \Slim\App();

$container = $app->getContainer();

$availableLang  = ['fr', 'en'];
$defaultLang = 'en';

//This parameter must be is instance of TWIG Environment! /!\ (no require)
$twigEnvironment = $container['view'];

/*
 * this middleware will add 'lang' container with lang slug (ex: fr) and create global variable 'lang' in twig 
   environment
 */
$app->add(new \lefuturiste\Slim3_MultiLanguage\MultilanguageMiddleware([
    'availableLang' => $availableLang,
    'defaultLang' => $defaultLang,
    'twig' => $twigEnvironment
]));

$app->get('/no-page-multilanguage-support', 'CALLED FONCTION');

$app->group('/{lang:[a-z]{2}}', function () use ($container){
    
    var_dump($container->lang);

    //route for /{lang}
    $this->get('', 'CALLED FONCTION')->setName('home');
    
    //route for /{lang}/contact
    $this->get('/contact', 'CALLED FONCTION')->setName('contact');

});

$app->run();

```

