# Simplyhired Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-simplyhired.svg?style=flat-square)](https://github.com/JobBrander/jobs-simplyhired/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-simplyhired/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-simplyhired)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-simplyhired.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-simplyhired/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-simplyhired.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-simplyhired)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-simplyhired.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-simplyhired)

This package provides Simplyhired Jobs API support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-simplyhired
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Simplyhired` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Simplyhired([
    'developerKey' => 'YOUR SIMPLYHIRED DEVELOPER KEY',
    'ipAddress' => 'YOUR IP ADDRESS',
]);

// Search for 200 job listings for 'project manager' in Chicago, IL
$jobs = $client->setKeyword('project manager')
    ->setCity('Chicago')
    ->setState('IL')
    ->setCount(200)
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-simplyhired/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Karl Hughes](https://github.com/karllhughes)
- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/jobbrander/jobs-simplyhired/contributors)

## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-simplyhired/blob/master/LICENSE) for more information.
