# PHP Livereload

[![Build Status](https://travis-ci.org/RickySu/php-livereload.svg?branch=master)](https://travis-ci.org/RickySu/php-livereload)

php-livereload is a livereload server written in PHP.

php-livereload uses [livereload.js](https://github.com/livereload/livereload-js) -- a JavaScript file implementing the client side of the LiveReload protocol.

## Install

Install php-livereload from [composer](http://getcomposer.org).

```JSON
{
    "require": {
        "rickysu/php-livereload": "dev-master"
    }
}
```

Get the command-line php-livereload

    $ curl -O https://raw.github.com/RickySu/php-livereload/master/dist/reload.phar
    $ chmod +x reload.phar
    $ sudo mv reload.phar /usr/bin

Install [LiveReload Safari/Chrome/Firefox extension](http://feedback.livereload.com/knowledgebase/articles/86242-how-do-i-install-and-use-the-browser-extensions-)

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

#### Initialize a default livereload.json file.

```
$ php bin/reload livereload:init
```

#### Running Server.

```
$ php bin/reload server:run
```

#### Rolling Your Own Live Reload

 If you would like to trigger the live reload server yourself,
simply POST files to the URL: http://localhost:35729/changed.
Or if you rather roll your own live reload implementation use the following example:

```
# notify a single change
curl http://localhost:35729/changed?files=style.css

# notify using a longer path
curl http://localhost:35729/changed?files=js/app.js

# notify multiple changes, comma or space delimited
curl http://localhost:35729/changed?files=index.html,style.css,docs/docco.css
```

Or you can bulk the information into a POST request, with body as a JSON array of files.

```
curl -X POST http://localhost:35729/changed -d '{ "files": ["style.css", "app.js"] }'

# from a JSON file
node -pe 'JSON.stringify({ files: ["some.css", "files.css"] })' > files.json
curl -X POST -d @files.json http://localhost:35729
```

## License

MIT, see LICENSE.
