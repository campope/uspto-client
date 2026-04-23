# USPTO Client

Laravel package for working with USPTO APIs through a small service layer, resource classes, and response DTOs.

## Requirements

- PHP 8.2+
- Laravel 12+

## Installation

Install the package with Composer:

```bash
composer require radicaldreamers/uspto-client
```

If you are pulling from a private Git repository instead of Packagist, add a VCS repository in the consuming project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:radicaldreamers/uspto-client.git"
    }
  ],
  "require": {
    "radicaldreamers/uspto-client": "dev-main"
  }
}
```

For private repositories, authenticate Composer with a GitHub, GitLab, or other VCS token appropriate for your host.

## Configuration

Laravel package discovery will register the service provider automatically.

To publish the package config:

```bash
php artisan vendor:publish --tag=uspto-client-config
```

Available config values:

```php
return [
    'base_url' => env('USPTO_BASE_URL', 'https://api.uspto.gov'),
    'timeout' => (int) env('USPTO_TIMEOUT', 120),
    'connect_timeout' => (int) env('USPTO_CONNECT_TIMEOUT', 10),
    'verify' => env('USPTO_VERIFY_SSL', true),
    'headers' => [
        'Accept' => 'application/json',
        'X-API-KEY' => env('USPTO_API_KEY'),
    ],
];
```

## Usage

Use the facade or resolve the service from the container.

### Patent applications

```php
use RadicalDreamers\UsptoClient\USPTO;

$application = USPTO::patentApplications()->find('17912345');

$searchResults = USPTO::patentApplications()->search([
    'q' => 'artificial intelligence',
    'pageNum' => 1,
    'pageSize' => 25,
]);
```

### Service injection

```php
use RadicalDreamers\UsptoClient\USPTOService;

class PatentLookupController
{
    public function __invoke(USPTOService $uspto)
    {
        return $uspto->patentApplications()->query([
            'q' => 'robotics',
            'pageNum' => 1,
            'pageSize' => 10,
        ]);
    }
}
```

### PEDS lookup

```php
$peds = USPTO::peds()->query('17912345');

$summary = $peds->toArray();
```

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for release notes.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md).

## Security

If you discover a security issue, email `cam.pope@gmail.com` instead of opening a public issue.

## License

This package is proprietary and intended for private use.
