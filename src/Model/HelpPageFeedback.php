<?php

namespace Tipbr\HelpCentre\Model;

use SilverStripe\ORM\DataObject;
use Tipbr\HelpCentre\Pages\HelpPage;

class HelpPageFeedback extends DataObject
{
    private static string $table_name = 'HelpCentre_HelpPageFeedback';

    private static array $db = [
        'Helpful' => "Enum('Yes,No','Yes')",
        'Comment' => 'Text',
    ];

    private static array $has_one = [
        'HelpPage' => HelpPage::class,
    ];

    private static string $default_sort = 'Created DESC';

    private static array $summary_fields = [
        'Created.Nice' => 'Submitted',
        'Helpful' => 'Helpful',
        'Comment' => 'Comment',
    ];
}
