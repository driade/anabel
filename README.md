# Check by the browser the status of the packages in your composer.json

[![Latest Stable Version](https://poser.pugx.org/driade/anabel/v/stable)](https://packagist.org/packages/driade/anabel)
[![Latest Unstable Version](https://poser.pugx.org/driade/anabel/v/unstable)](https://packagist.org/packages/driade/anabel)
[![Build Status](https://travis-ci.org/driade/anabel.svg?branch=master)](https://travis-ci.org/driade/anabel)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/driade/anabel/badges/quality-score.png?b=master&s=ae081333c0aa2e8edfb78c02e1db803e0bbb4ed3)](https://scrutinizer-ci.com/g/driade/anabel/?branch=master)
[![License](https://poser.pugx.org/driade/anabel/license)](https://packagist.org/packages/driade/anabel)
[![Total Downloads](https://poser.pugx.org/driade/anabel/downloads)](https://packagist.org/packages/driade/anabel)

Anabel provides a visual bridge to the command "composer outdated", in order to review the status of the packages you're using in your project.


![Alt text](/docs/screenshot.png?raw=true "Screenshot")


## Installation

You can install the package via composer:

```bash
composer require driade/anabel
```

## Usage

You may display Anabel results, usually, in a route under the admin area of your site. Just add this lines to your controller and you should see the results.

```php
$anabel = new \Driade\Anabel\Anabel;

$anabel->setConfig(['all' => true, 'composer_dir' => __DIR__, 'sort' => true]);
$anabel->outdated();

echo $anabel->render();
```

Anabel will display the output in HTML using Bootstrap.

## Configuration

You can pass an array to setConfig in order to control the behaviour of the package. These are the available options:

```php
[
    'all'             => false, // Whether to show all the packages or just which should need an update
    'composer_dir'    => '.', // Directory where the file composer.json is located
    'templates_dir'   => __DIR__ . '/views', // Directory where the templates for the output are located
    'template_header' => 'header.twig.php', // Header of the page, supports Twig
    'template_body'   => 'body.twig.php', // Body of the page, supports Twig
    'template_footer' => 'footer.twig.php', // Footer of the page, supports Twig
    'sort'            => true, // Whether to sort the packages by the need of an update
];
```

## Memory issues

This program runs in the browser and calls Composer via API, and it's possible that you have a low memory limit in your php.ini that will make Composer fail.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information of what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email david@mundosaparte.com instead of using the issue tracker.

## Credits

- [David Fernández] (https://github.com/driade) (https://twitter.com/davidfrafael)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
