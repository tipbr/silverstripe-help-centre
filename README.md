# Silverstripe Help Centre

Reusable Silverstripe 6 module for documentation/help-centre sites using page types plus Elemental blocks.

## Features

- Page types: `HelpDesk` → `HelpSection` → `HelpPage`
- Article navigation: sidebar, breadcrumbs, in-page ToC, previous/next links
- Optional article metadata: author, status, reading time, topics, related articles
- Feedback endpoint (`POST /feedback`) with CSRF validation and comment length limit
- Optional public JSON endpoint for helpdesk structure (`GET /{helpdesk-link}/api`)
- Elemental blocks:
  - `HelpContentBlock` (section + anchor)
  - `HelpFaqBlock` (FAQ item)
  - `HelpCalloutBlock` (`Info`, `Tip`, `Warning`)
- Optional Google Analytics hooks (`help_article_view`, `help_search`, `help_feedback_submitted`)

## Requirements

- PHP `^8.3`
- `silverstripe/framework` `^6`
- `silverstripe/cms` `^6`
- `dnadesign/silverstripe-elemental` `^6.2`

## Installation

```bash
composer require tipbr/silverstripe-help-centre
vendor/bin/sake dev/build flush=all
```

## Usage

1. Create a `Help Desk` page at the site root.
2. Add `Help Section` pages under it.
3. Add `Help Page` pages under each section.
4. Add Help blocks (`Help Content`, `Help FAQ`, `Help Callout`) to each article.
5. Configure metadata and related articles in the `Help Metadata` tab.

## Public API endpoint

You can expose a read-only public endpoint for the helpdesk tree (sections + pages).

Enable it in YAML:

```yml
Tipbr\HelpCentre\Pages\HelpDesk:
  public_api_enabled: true
```

Then run:

```bash
vendor/bin/sake dev/build flush=all
```

Endpoint format:

- `GET /{your-helpdesk-page}/api`

When disabled (default), the endpoint returns `404`.

## Templates to override

- `templates/SilverStripeHelpCentre/Pages/Layout/HelpPage.ss`
- `templates/SilverStripeHelpCentre/Pages/Layout/HelpSection.ss`
- `templates/SilverStripeHelpCentre/Blocks/HelpContentBlock.ss`
- `templates/SilverStripeHelpCentre/Blocks/HelpFaqBlock.ss`
- `templates/SilverStripeHelpCentre/Blocks/HelpCalloutBlock.ss`

## Namespaces

- `SilverStripeHelpCentre\Pages\`
- `SilverStripeHelpCentre\Blocks\`
- `SilverStripeHelpCentre\Model\`

## Upgrade note

If upgrading from an internal/pre-publish version with different namespaces or table prefixes, run `dev/build` and review migration SQL before deployment.

## License

BSD-3-Clause
