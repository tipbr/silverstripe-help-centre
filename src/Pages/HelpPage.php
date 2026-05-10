<?php

namespace ForwardFi\Pages;

use ForwardFi\Blocks\HelpContentBlock;
use Page;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;

class HelpPage extends Page
{
    private static string $table_name    = 'ForwardFi_HelpPage';
    private static string $singular_name = 'Help Page';
    private static string $plural_name   = 'Help Pages';
    private static string $description   = 'A documentation page with structured content blocks.';
    private static string $icon_class    = 'font-icon-list';

    private static array $db = [];

    private static array $allowed_children = [];
    private static array $allowed_parents  = [HelpSection::class];
    
    private static bool $can_be_root = false;

    /**
     * Return the root HelpDesk ancestor, or null if structure is unexpected.
     */
    public function HelpDesk(): ?HelpDesk
    {
        $section = $this->Parent();
        if (!($section instanceof HelpSection)) {
            return null;
        }
        $desk = $section->Parent();
        return ($desk instanceof HelpDesk) ? $desk : null;
    }

    /**
     * Return all HelpSections under the same HelpDesk, each with a NavPages
     * list of their HelpPage children. Used by the sidebar template.
     */
    public function HelpNav(): ArrayList
    {
        $nav     = ArrayList::create();
        $helpDesk = $this->HelpDesk();

        if (!$helpDesk) {
            return $nav;
        }

        foreach ($helpDesk->Children()->filter('ClassName', HelpSection::class) as $section) {
            $pages = ArrayList::create();
            foreach ($section->Children()->filter('ClassName', HelpPage::class) as $page) {
                $pages->push(ArrayData::create([
                    'Title'         => $page->MenuTitle,
                    'Link'          => $page->Link(),
                    'IsCurrentPage' => ($page->ID === $this->ID),
                ]));
            }

            $nav->push(ArrayData::create([
                'Title'          => $section->Title,
                'Link'           => $section->Link(),
                'IsCurrentSection' => ($section->ID === $this->ParentID),
                'NavPages'       => $pages,
            ]));
        }

        return $nav;
    }

    /**
     * Return only the HelpContentBlocks attached to this page, for ToC generation.
     */
    public function HelpContentBlocks(): DataList
    {
        return HelpContentBlock::get()->filter([
            'ParentID' => $this->ElementalArea()->ID,
        ]);
    }

    /**
     * Return an ordered flat list of all HelpPages across all sections.
     */
    private function allPagesInOrder(): array
    {
        $helpDesk = $this->HelpDesk();
        if (!$helpDesk) {
            return [];
        }

        $pages = [];
        foreach ($helpDesk->Children()->filter('ClassName', HelpSection::class) as $section) {
            foreach ($section->Children()->filter('ClassName', HelpPage::class) as $page) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    public function PreviousHelpPage(): ?HelpPage
    {
        $pages = $this->allPagesInOrder();
        for ($i = 0; $i < count($pages); $i++) {
            if ($pages[$i]->ID === $this->ID) {
                return $i > 0 ? $pages[$i - 1] : null;
            }
        }
        return null;
    }

    public function NextHelpPage(): ?HelpPage
    {
        $pages = $this->allPagesInOrder();
        for ($i = 0; $i < count($pages); $i++) {
            if ($pages[$i]->ID === $this->ID) {
                return isset($pages[$i + 1]) ? $pages[$i + 1] : null;
            }
        }
        return null;
    }
}
