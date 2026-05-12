<?php

namespace Tipbr\HelpCentre\Pages;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

class HelpDeskController extends Controller
{
    private static array $allowed_actions = [
        'api',
    ];

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

    public function api(HTTPRequest $request): HTTPResponse
    {
        if (!$this->data()->config()->get('public_api_enabled')) {
            return HTTPResponse::create('Not Found', 404);
        }

        if (!$request->isGET()) {
            return HTTPResponse::create('Method Not Allowed', 405);
        }

        $helpDesk = $this->data();
        $sections = [];

        foreach ($helpDesk->Children()->filter('ClassName', HelpSection::class)->sort('Sort') as $section) {
            if (!$section->canView(null)) {
                continue;
            }

            $pages = [];
            foreach ($section->Children()->filter('ClassName', HelpPage::class)->sort('Sort') as $page) {
                if (!$page->canView(null)) {
                    continue;
                }

                $pages[] = [
                    'id' => (int) $page->ID,
                    'title' => (string) $page->Title,
                    'menu_title' => (string) ($page->MenuTitle ?: $page->Title),
                    'link' => (string) $page->Link(),
                    'content' => trim(strip_tags((string) ($page->Content ?? ''))),
                    'author_name' => (string) $page->AuthorName,
                    'reading_time_label' => (string) $page->ReadingTimeLabel(),
                    'article_status' => (string) $page->ArticleStatus,
                    'topics' => $page->TopicList(),
                ];
            }

            $sections[] = [
                'id' => (int) $section->ID,
                'title' => (string) $section->Title,
                'menu_title' => (string) ($section->MenuTitle ?: $section->Title),
                'link' => (string) $section->Link(),
                'pages' => $pages,
            ];
        }

        $payload = json_encode([
            'helpdesk' => [
                'id' => (int) $helpDesk->ID,
                'title' => (string) $helpDesk->Title,
                'menu_title' => (string) ($helpDesk->MenuTitle ?: $helpDesk->Title),
                'link' => (string) $helpDesk->Link(),
            ],
            'sections' => $sections,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($payload === false) {
            return HTTPResponse::create('Failed to encode response', 500);
        }

        $response = HTTPResponse::create($payload, 200);
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }
}
