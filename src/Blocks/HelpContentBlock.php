<?php

namespace SilverStripeHelpCentre\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\View\Parsers\URLSegmentFilter;

class HelpContentBlock extends BaseElement
{
    private static string $table_name    = 'HelpCentre_HelpContentBlock';
    private static string $singular_name = 'Help Content Block';
    private static string $icon          = 'font-icon-block-content';
    private static string $description   = 'A titled content section with anchor link. Restricted to Help pages.';

    private static array $db = [
        'Title'   => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Section Title'),
            HTMLEditorField::create('Content')->setRows(15),
        ]);
        return $fields;
    }

    /**
     * URL-safe anchor slug derived from the section title.
     * Used as the id attribute on headings so the ToC links work.
     */
    public function Anchor(): string
    {
        $slug = URLSegmentFilter::create()->filter((string) $this->Title);
        return $slug ?: 'section-' . $this->ID;
    }

    public function getType(): string
    {
        return 'Help Content';
    }
}
