# Silverstripe Help Centre

A reusable Silverstripe module for building help centre content using a simple page hierarchy and Elemental blocks.

## Features

- `HelpDesk` top-level landing page type
- `HelpSection` grouping page type
- `HelpPage` content page type with:
  - sidebar navigation
  - on-page table of contents
  - previous/next navigation
- `HelpContentBlock` Elemental block type with anchor links

## Requirements

- PHP `^8.3`
- `silverstripe/framework` `^6`
- `silverstripe/cms` `^6`
- `dnadesign/silverstripe-elemental` `^6.2`

## Installation

Install with Composer:

```bash
composer require tipbr/silverstripe-help-centre
```

Then run a dev/build:

```bash
vendor/bin/sake dev/build flush=all
```

## Usage

1. In the CMS, create a `Help Desk` page at the site root.
2. Add one or more `Help Section` pages under it.
3. Add `Help Page` pages under each section.
4. Add `Help Content Block` blocks to each help page via Elemental.

Templates are provided at:

- `templates/SilverStripeHelpCentre/Pages/Layout/HelpPage.ss`
- `templates/SilverStripeHelpCentre/Blocks/HelpContentBlock.ss`

Override them in your project or theme as needed.

## Namespaces

This module uses the following PSR-4 namespaces:

- `SilverStripeHelpCentre\Pages\`
- `SilverStripeHelpCentre\Blocks\`

## Notes for Existing Installations

If you are upgrading from an internal or pre-publish version that used different namespace or table-name prefixes, run `dev/build` and review generated database migration changes before deploying.

## License

BSD-3-Clause
