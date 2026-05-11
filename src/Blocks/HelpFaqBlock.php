<?php

namespace SilverStripeHelpCentre\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

class HelpFaqBlock extends BaseElement
{
    private static string $table_name    = 'HelpCentre_HelpFaqBlock';
    private static string $singular_name = 'Help FAQ Block';
    private static string $icon          = 'font-icon-help-circled';
    private static string $description   = 'A FAQ accordion item with a question and answer.';

    private static array $db = [
        'Question' => 'Varchar(255)',
        'Answer' => 'HTMLText',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Question', 'Question'),
            HTMLEditorField::create('Answer', 'Answer')->setRows(10),
        ]);
        return $fields;
    }

    public function getType(): string
    {
        return 'Help FAQ';
    }
}
