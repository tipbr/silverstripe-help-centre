<?php

namespace Tipbr\HelpCentre\Pages;

use Page;

class HelpDesk extends Page
{
    private static string $table_name    = 'HelpCentre_HelpDesk';
    private static string $singular_name = 'Help Desk';
    private static string $plural_name   = 'Help Desks';
    private static string $description   = 'Top-level help centre landing page.';
    private static string $icon_class    = 'font-icon-help-circled';

    private static array $db = [];

    private static array $allowed_children = [HelpSection::class];
}
