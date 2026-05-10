<?php

namespace ForwardFi\Pages;

use PageController;
use SilverStripe\Control\HTTPResponse;

class HelpDeskController extends PageController
{
    public function index(): HTTPResponse|array
    {
        $first = $this->data()->Children()
            ->filter('ClassName', HelpSection::class)
            ->first();

        if ($first) {
            return $this->redirect($first->Link());
        }

        return [];
    }
}
