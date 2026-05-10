<?php

namespace SilverStripeHelpCentre\Pages;

use PageController;
use SilverStripe\Control\HTTPResponse;

class HelpSectionController extends PageController
{
    public function index(): HTTPResponse|array
    {
        $first = $this->data()->Children()
            ->filter('ClassName', HelpPage::class)
            ->sort('Sort')
            ->first();

        if ($first) {
            return $this->redirect($first->Link());
        }

        return [];
    }
}
