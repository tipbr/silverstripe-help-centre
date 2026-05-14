<?php

namespace Tipbr\HelpCentre\Pages;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\HTTPResponse;

class HelpDeskController extends ContentController
{
    public function index(): HTTPResponse|array
    {
        $first = $this->data()->Children()
            ->filter('ClassName', HelpSection::class)
            ->sort('Sort')
            ->first();

        if ($first) {
            return $this->redirect($first->Link());
        }

        return [];
    }
}
