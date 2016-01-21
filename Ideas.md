## Random thoughts ##
  * Model
  * View
  * Controller
  * Component-based Architecture
  * Security
  * UTF-8 / Internationalization
  * Debugging

### Model ###
  * Use [Doctrine](http://www.phpdoctrine.org/)?
  * Use [Creole](http://creole.phpdb.org/trac/) for database abstraction?
  * Or use my own! Yay! (I just wish somebody created easy interfaces for ORM in PHP. The ones I've seen so far are just... inelegant.)
```
<?php

class MyPet extends Asar_Model {
  public static function definition() {
    self::property('name', 'string')
    self::property('species', 'string', array('default' => 'dog'));
    self::property('birthday', 'date');
  }
}

// ... inside a controller ...

$mypet = $this->model['MyPet'];
$mypet->name = 'Scratch'; // Validation starts here already
$mypet->species = 'cat';
$mypet->birthday = 'January 11, 2007';

?>
```

### View ###
  * Views as _representations of resources_

### Controller ###
  * Controllers are resources
  * Resources describe themselves? ([RDF](http://en.wikipedia.org/wiki/Resource_Description_Framework)?)
  * Do we need a **Dispatcher** so that the call stack wouldn't be so deep

### Component-based Architecture ###
  * Maybe we can implement some sort of component-based architecture where each component consists of a model, view, controller and using the same set of interfaces to interact with components
  * So we can think of the whole web application as one big component and then can ask for services provided by other components
  * Applications can also be clients! (class Name\_Application extends Asar\_Application implements Asar\_Client\_Interface)
  * [Component-based Software Engineering](http://en.wikipedia.org/wiki/Component-based_software_engineering)
  * [Component-based Scalable Logical Architecture](http://en.wikipedia.org/wiki/Component-based_Scalable_Logical_Architecture)
  * [Content-Negotiation](http://en.wikipedia.org/wiki/Content_negotiation)

### Security ###
  * Proper input validation <- How?
  * Magic Quotes <- [How to disable magic quotes](http://www.php.net/manual/en/security.magicquotes.disabling.php)
  * [PHP Security Tips](http://www.sitepoint.com/article/php-security-blunders)
  * [Error-reporting](http://www.php.net/errorfunc)
  * Access Control?

### UTF-8 / Internationalization ###
  * [PHP UTF-8 cheatsheet](http://www.nicknettleton.com/zine/php/php-utf-8-cheatsheet)
  * [Handling UTF-8 with PHP](http://www.phpwact.org/php/i18n/utf-8)

### Debugging ###
  * Error-reporting and debug mode directive a bit like coldfusion's
  * [Debug with Firebug](http://forenblogger.de/2007/02/14/how-to-debug-php-with-firebug/). nice?

### Javascript ###
  * Use [jQuery](http://jquery.com/) for Javascript Framework with [Easing Plugin](http://gsgd.co.uk/sandbox/jquery/easing/)

### Forms ###

### Helpers (HTML, Forms) ###

### Misc ###
  * [Apache performance tuning](http://httpd.apache.org/docs/2.2/misc/perf-tuning.html#runtime)