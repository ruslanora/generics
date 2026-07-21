# Contributing

Thanks for taking the time to contribute!

## Contributing flow

1. Clone the repository.
2. Create a branch off `main` for your change.
3. Make your change, with tests for any new or changed behavior.
4. Open a pull request against `main`.
5. CI must pass (code style, static analysis, tests) before the PR can be merged.
6. Once approved, the PR is squash-merged into `main`.

`main` is always releasable — there is no separate `prod` branch. Versions are cut as [GitHub Releases](https://github.com/ruslanora/generics/releases) tagged directly off `main`.

## Getting started

Install dependencies:

```sh
composer install
```

## Development workflow

Before opening a pull request, make sure the following pass — this is the same set of checks CI runs:

```sh
composer format:check   # code style (php-cs-fixer)
composer analyse         # static analysis (phpstan, level max)
composer test            # test suite (phpunit)
```

You can automatically fix code style issues with:

```sh
composer format
```

## Guidelines

- Target PHP 8.1+ syntax and features.
- Follow PSR-12; formatting is enforced by php-cs-fixer, so run `composer format` before committing.
- Add or update tests for any behavioral change. Pull requests without tests will be closed.
- Keep pull requests focused on a single change and include a clear, descriptive title and description.

## Reporting issues

Use the GitHub issue templates for bug reports and feature requests. For security vulnerabilities, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.
