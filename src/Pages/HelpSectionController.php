<?php

namespace ForwardFi\Pages;

use PageController;
use SilverStripe\Control\HTTPResponse;

class HelpSectionController extends PageController
{
    public function index(): HTTPResponse|array
    {
        $first = $this->data()->Children()
            ->filter('ClassName', HelpPage::class)
            ->first();

        if ($first) {
            return $this->redirect($first->Link());
        }

        return [];
    }
}
