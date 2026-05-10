<?php

namespace ForwardFi\Pages;

use Page;

class HelpSection extends Page
{
    private static string $table_name    = 'ForwardFi_HelpSection';
    private static string $singular_name = 'Help Section';
    private static string $plural_name   = 'Help Sections';
    private static string $description   = 'A section grouping related help pages.';
    private static string $icon_class    = 'font-icon-box';

    private static array $db = [];

    private static array $allowed_children = [HelpPage::class];
    private static array $allowed_parents  = [HelpDesk::class];
    
    private static bool $can_be_root = false;
}
