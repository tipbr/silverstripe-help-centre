<?php

namespace SilverStripeHelpCentre\Pages;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\SecurityToken;
use SilverStripeHelpCentre\Model\HelpPageFeedback;

class HelpPageController extends PageController
{
    private static array $allowed_actions = [
        'feedback',
    ];

    public function feedback(HTTPRequest $request): HTTPResponse
    {
        if (!$request->isPOST()) {
            return HTTPResponse::create('Method Not Allowed', 405);
        }

        if (!SecurityToken::inst()->checkRequest($request)) {
            return HTTPResponse::create('Invalid security token', 400);
        }

        $helpful = strtolower((string) $request->postVar('Helpful'));
        if (!in_array($helpful, ['yes', 'no'], true)) {
            return HTTPResponse::create('Invalid feedback value', 400);
        }

        $feedback = HelpPageFeedback::create();
        $feedback->HelpPageID = (int) $this->data()->ID;
        $feedback->Helpful = $helpful === 'yes' ? 'Yes' : 'No';
        $feedback->Comment = trim((string) $request->postVar('Comment'));
        $feedback->write();

        $response = HTTPResponse::create(json_encode([
            'ok' => true,
            'helpful' => $feedback->Helpful,
        ]), 200);
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    public function SecurityID(): string
    {
        return SecurityToken::inst()->getValue();
    }
}
