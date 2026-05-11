<?php

namespace SilverStripeHelpCentre\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

class HelpCalloutBlock extends BaseElement
{
    private static string $table_name    = 'HelpCentre_HelpCalloutBlock';
    private static string $singular_name = 'Help Callout Block';
    private static string $icon          = 'font-icon-attention';
    private static string $description   = 'An admonition/callout block (info, tip, warning).';

    private static array $db = [
        'CalloutType' => "Enum('Info,Tip,Warning','Info')",
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create('CalloutType', 'Type', [
                'Info' => 'Info',
                'Tip' => 'Tip',
                'Warning' => 'Warning',
            ]),
            TextField::create('Title', 'Title (optional)'),
            HTMLEditorField::create('Content')->setRows(10),
        ]);
        return $fields;
    }

    public function ThemeClass(): string
    {
        return match ((string) $this->CalloutType) {
            'Tip' => 'border-emerald-500 bg-emerald-500/10 text-emerald-900',
            'Warning' => 'border-amber-500 bg-amber-500/10 text-amber-900',
            default => 'border-sky-500 bg-sky-500/10 text-sky-900',
        };
    }

    public function DisplayTitle(): string
    {
        if ($this->Title) {
            return (string) $this->Title;
        }
        return (string) $this->CalloutType;
    }

    public function getType(): string
    {
        return 'Help Callout';
    }
}
