<?php

namespace SilverStripeHelpCentre\Pages;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Security\SecurityToken;
use SilverStripeHelpCentre\Model\HelpPageFeedback;

class HelpPageController extends PageController
{
    private const int MAX_COMMENT_LENGTH = 2000;

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

        $comment = strip_tags((string) $request->postVar('Comment'));
        $comment = trim($comment);
        if (strlen($comment) > self::MAX_COMMENT_LENGTH) {
            $comment = substr($comment, 0, self::MAX_COMMENT_LENGTH);
        }

        $feedback = HelpPageFeedback::create();
        $feedback->HelpPageID = (int) $this->data()->ID;
        $feedback->Helpful = $helpful === 'yes' ? 'Yes' : 'No';
        $feedback->Comment = $comment;
        $feedback->write();

        $payload = json_encode([
            'ok' => true,
            'helpful' => $feedback->Helpful,
        ]);
        if ($payload === false) {
            return HTTPResponse::create('Failed to encode response', 500);
        }

        $response = HTTPResponse::create($payload, 200);
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }

    public function SecurityID(): string
    {
        return SecurityToken::inst()->getValue();
    }
}
