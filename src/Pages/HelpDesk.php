<?php

namespace Tipbr\HelpCentre\Pages;

use SilverStripe\CMS\Model\SiteTree;

class HelpDesk extends SiteTree
{
    private static string $table_name    = 'HelpCentre_HelpDesk';
    private static string $singular_name = 'Help Desk';
    private static string $plural_name   = 'Help Desks';
    private static string $description   = 'Top-level help centre landing page.';
    private static string $icon_class    = 'font-icon-help-circled';

    private static array $db = [];

    private static array $allowed_children = [HelpSection::class];

    /**
     * Enable a public JSON endpoint at /{helpdesk-link}/api.
     * Configure via YAML:
     * Tipbr\HelpCentre\Pages\HelpDesk:
     *   public_api_enabled: true
     */
    private static bool $public_api_enabled = false;
}
