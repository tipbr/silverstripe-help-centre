<?php

namespace Tipbr\HelpCentre\Pages;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;

class HelpDeskController extends Controller
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
