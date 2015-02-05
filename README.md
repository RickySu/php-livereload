# PHP Livereload

[![Build Status](https://travis-ci.org/RickySu/php-livereload.svg?branch=master)](https://travis-ci.org/RickySu/php-livereload)

php-livereload is a livereload server written in PHP.

php-livereload uses [livereload.js](https://github.com/livereload/livereload-js) -- a JavaScript file implementing the client side of the LiveReload protocol.

## Install

The recommended way to install php-livereload is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "rickysu/php-livereload": "dev-master"
    }
}
```

## Tests

To run the test suite, you need install the dependencies via composer, then
run PHPUnit.

    $ composer install
    $ phpunit

## Using php-livereload
define a livereload.json in your project root.

livereload.json

```JSON
{
    "period": 1,
    "watch": {
        "web/css/":   "*.css",
        "web/js/":    "*.js",
        "web/img/":   "\\.png|gif|jpg$"
    }
}
```

* period:  monitor file changes every 1 second.
* watch: file and folder you want to watch

#### initialize a default livereload.json file.

```
$ php bin/reload livereload:init
```


#### running server.

```
$ php bin/reload livereload:run
```

## License

MIT, see LICENSE.
