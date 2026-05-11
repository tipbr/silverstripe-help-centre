<?php

namespace Tipbr\HelpCentre\Pages;

use Tipbr\HelpCentre\Blocks\HelpContentBlock;
use Page;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use Tipbr\HelpCentre\Model\HelpPageFeedback;

class HelpPage extends Page
{
    private const int DEFAULT_RELATED_ARTICLE_LIMIT = 4;
    private const int DEFAULT_WORDS_PER_MINUTE = 200;
    private ?string $readingTimeLabelCache = null;

    private static string $table_name    = 'HelpCentre_HelpPage';
    private static string $singular_name = 'Help Page';
    private static string $plural_name   = 'Help Pages';
    private static string $description   = 'A documentation page with structured content blocks.';
    private static string $icon_class    = 'font-icon-list';

    private static array $db = [
        'AuthorName' => 'Varchar(255)',
        'ReadingTimeMinutes' => 'Int',
        'ArticleStatus' => "Enum('Draft,Reviewed,Published,Deprecated','Published')",
        'Topics' => 'Text',
    ];

    private static array $allowed_children = [];
    private static array $allowed_parents  = [HelpSection::class];

    private static array $many_many = [
        'RelatedArticles' => HelpPage::class,
    ];

    private static array $has_many = [
        'FeedbackSubmissions' => HelpPageFeedback::class,
    ];
    
    private static bool $can_be_root = false;

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.HelpMetadata', [
            TextField::create('AuthorName', 'Author'),
            NumericField::create('ReadingTimeMinutes', 'Reading time (minutes)'),
            DropdownField::create('ArticleStatus', 'Status', [
                'Draft' => 'Draft',
                'Reviewed' => 'Reviewed',
                'Published' => 'Published',
                'Deprecated' => 'Deprecated',
            ]),
            TextareaField::create('Topics', 'Tags / Topics')
                ->setDescription('Comma-separated topics, e.g. billing, setup, api'),
        ]);

        $relatedSource = self::get()->exclude('ID', (int) $this->ID)->sort('Title')->map('ID', 'Title')->toArray();
        $fields->addFieldToTab(
            'Root.HelpMetadata',
            ListboxField::create('RelatedArticles', 'Related articles', $relatedSource)
        );

        return $fields;
    }

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

        foreach ($helpDesk->Children()->filter('ClassName', HelpSection::class)->sort('Sort') as $section) {
            $pages = ArrayList::create();
            foreach ($section->Children()->filter('ClassName', HelpPage::class)->sort('Sort') as $page) {
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
        $area = $this->ElementalArea();
        if (!$area || !$area->exists()) {
            return HelpContentBlock::get()->where('1 = 0');
        }

        return HelpContentBlock::get()->filter([
            'ParentID' => $area->ID,
        ])->sort('Sort');
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
        foreach ($helpDesk->Children()->filter('ClassName', HelpSection::class)->sort('Sort') as $section) {
            foreach ($section->Children()->filter('ClassName', HelpPage::class)->sort('Sort') as $page) {
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

    public function TopicList(): array
    {
        if (!$this->Topics) {
            return [];
        }
        $parts = preg_split('/[,\\r\\n]+/', (string) $this->Topics) ?: [];
        $topics = [];
        foreach ($parts as $part) {
            $topic = trim((string) $part);
            if ($topic !== '') {
                $topics[] = $topic;
            }
        }
        return array_values(array_unique($topics));
    }

    public function TopicItems(): ArrayList
    {
        $list = ArrayList::create();
        foreach ($this->TopicList() as $topic) {
            $list->push(ArrayData::create(['Title' => $topic]));
        }
        return $list;
    }

    public function RelatedHelpPages(): ArrayList
    {
        $limit = self::DEFAULT_RELATED_ARTICLE_LIMIT;
        $items = ArrayList::create();

        foreach ($this->RelatedArticles()->exclude('ID', $this->ID)->limit($limit) as $page) {
            $items->push($page);
        }

        if ($items->count() >= $limit) {
            return $items;
        }

        $currentTopics = $this->TopicList();
        if (!$currentTopics) {
            return $items;
        }

        $existingIds = [];
        foreach ($items as $item) {
            $existingIds[] = (int) $item->ID;
        }
        $existingIds[] = (int) $this->ID;

        $scores = [];
        foreach (self::get()->exclude('ID', $existingIds) as $candidate) {
            $overlap = array_intersect($currentTopics, $candidate->TopicList());
            if ($overlap) {
                $scores[] = [
                    'score' => count($overlap),
                    'page' => $candidate,
                ];
            }
        }

        usort($scores, static fn(array $a, array $b) => $b['score'] <=> $a['score']);
        foreach ($scores as $scored) {
            if ($items->count() >= $limit) {
                break;
            }
            $items->push($scored['page']);
        }

        return $items;
    }

    public function ReadingTimeLabel(): string
    {
        if ($this->readingTimeLabelCache !== null) {
            return $this->readingTimeLabelCache;
        }

        $minutes = (int) $this->ReadingTimeMinutes;
        if ($minutes <= 0) {
            $text = strip_tags((string) $this->Title);
            foreach ($this->HelpContentBlocks() as $block) {
                $text .= ' ' . strip_tags((string) $block->Content);
            }
            $wordCount = str_word_count($text);
            $minutes = max(1, (int) ceil($wordCount / self::DEFAULT_WORDS_PER_MINUTE));
        }
        $this->readingTimeLabelCache = sprintf('%d min read', $minutes);
        return $this->readingTimeLabelCache;
    }
}
