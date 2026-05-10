<?php

namespace ForwardFi\Tasks;

use DNADesign\Elemental\Models\ElementalArea;
use ForwardFi\Blocks\HelpContentBlock;
use ForwardFi\Pages\HelpDesk;
use ForwardFi\Pages\HelpPage;
use ForwardFi\Pages\HelpSection;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use Symfony\Component\Console\Input\InputInterface;
use SilverStripe\PolyExecution\PolyOutput;

/**
 * Populates the help centre with a full user guide.
 *
 * Creates one HelpDesk page at the site root (or reuses the first existing
 * one), then builds HelpSection and HelpPage children under it, each with
 * HelpContentBlock elements that carry the guide text.
 *
 * Previously created nodes are identified by URLSegment so the task is
 * idempotent: running it twice will not produce duplicate pages.
 *
 * Run via: sake dev/tasks/ForwardFi-Tasks-PopulateHelpContentTask
 */
class PopulateHelpContentTask extends BuildTask
{
    private static string $segment = 'ForwardFi-Tasks-PopulateHelpContentTask';

    protected string $title = 'ForwardFi: Populate Help Centre Content';
    protected static string $description = 'Creates the Help Desk page hierarchy and fills it with user-guide content blocks. Safe to re-run.';

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

    public function execute(InputInterface $input, PolyOutput $output): int
    {
        $desk = $this->ensureHelpDesk();
        $this->log("Help Desk: \"{$desk->Title}\" (ID {$desk->ID})");

        foreach ($this->guide() as $sectionData) {
            $section = $this->ensureHelpSection($desk, $sectionData['title']);
            $this->log("  Section: \"{$section->Title}\"");

            foreach ($sectionData['pages'] as $pageData) {
                $page = $this->ensureHelpPage($section, $pageData['title']);
                $this->log("    Page: \"{$page->Title}\"");

                $area = $this->ensureElementalArea($page);

                foreach ($pageData['blocks'] as $blockData) {
                    $block = $this->ensureHelpContentBlock($area, $blockData['title'], $blockData['content']);
                    $this->log("      Block: \"{$block->Title}\"");
                }
            }
        }

        $this->log('Done.');
        return 0;
    }

    // -------------------------------------------------------------------------
    // Node helpers
    // -------------------------------------------------------------------------

    private function ensureHelpDesk(): HelpDesk
    {
        $existing = HelpDesk::get()->first();
        if ($existing) {
            return $existing;
        }

        $desk = HelpDesk::create();
        $desk->Title       = 'Help Centre';
        $desk->URLSegment  = 'help';
        $desk->ParentID    = 0;
        $desk->ShowInMenus = 1;
        $desk->write();
        $desk->publishRecursive();
        return $desk;
    }

    private function ensureHelpSection(HelpDesk $desk, string $title): HelpSection
    {
        $segment  = $this->slug($title);
        $existing = HelpSection::get()->filter([
            'ParentID'   => $desk->ID,
            'URLSegment' => $segment,
        ])->first();

        if ($existing) {
            return $existing;
        }

        $section = HelpSection::create();
        $section->Title      = $title;
        $section->URLSegment = $segment;
        $section->ParentID   = $desk->ID;
        $section->write();
        $section->publishRecursive();
        return $section;
    }

    private function ensureHelpPage(HelpSection $section, string $title): HelpPage
    {
        $segment  = $this->slug($title);
        $existing = HelpPage::get()->filter([
            'ParentID'   => $section->ID,
            'URLSegment' => $segment,
        ])->first();

        if ($existing) {
            return $existing;
        }

        $page = HelpPage::create();
        $page->Title      = $title;
        $page->URLSegment = $segment;
        $page->ParentID   = $section->ID;
        $page->write();
        $page->publishRecursive();
        return $page;
    }

    /**
     * Return the ElementalArea for a HelpPage, creating one if absent.
     */
    private function ensureElementalArea(HelpPage $page): ElementalArea
    {
        $area = $page->ElementalArea();
        if (!$area || !$area->exists()) {
            $area = ElementalArea::create();
            $area->write();
            $page->ElementalAreaID = $area->ID;
            $page->write();
            $page->publishRecursive();
        }
        return $area;
    }

    private function ensureHelpContentBlock(ElementalArea $area, string $title, string $content): HelpContentBlock
    {
        $existing = HelpContentBlock::get()->filter([
            'ParentID' => $area->ID,
            'Title'    => $title,
        ])->first();

        if ($existing) {
            return $existing;
        }

        $block           = HelpContentBlock::create();
        $block->Title    = $title;
        $block->Content  = $content;
        $block->ParentID = $area->ID;
        $block->write();
        return $block;
    }

    // -------------------------------------------------------------------------
    // User guide content
    // -------------------------------------------------------------------------

    /**
     * Returns the full user guide as a nested array:
     *   [ ['title' => 'Section', 'pages' => [ ['title' => 'Page', 'blocks' => [...]] ] ] ]
     */
    private function guide(): array
    {
        return [

            // ── Getting Started ───────────────────────────────────────────────
            [
                'title' => 'Getting Started',
                'pages' => [
                    [
                        'title' => 'Introduction to ForwardFi',
                        'blocks' => [
                            [
                                'title'   => 'What is ForwardFi',
                                'content' => '<p>ForwardFi is a personal cash-flow forecasting tool. It lets you model your expected income and expenses as recurring rules, then projects that activity forward so you can see whether you will have enough money in the months ahead.</p>
<p>Unlike budgeting apps that track what has already happened, ForwardFi focuses on what is going to happen. You define the rules for your regular transactions and the application generates a rolling 12-month forecast from them.</p>',
                            ],
                            [
                                'title'   => 'Key Concepts',
                                'content' => '<ul>
<li><strong>Bank Account</strong> - A named account that holds a current balance and groups your transaction rules.</li>
<li><strong>Transaction Rule</strong> - A named income or expense item with a recurrence pattern.</li>
<li><strong>Entry</strong> - A scheduling period attached to a rule that sets the amount, recurrence, and date range for that period.</li>
<li><strong>Forecast</strong> - A day-by-day projection of your balance generated from your rules.</li>
<li><strong>Share</strong> - An invitation that gives another ForwardFi user read and edit access to one of your accounts.</li>
</ul>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Creating Your Account',
                        'blocks' => [
                            [
                                'title'   => 'Registering',
                                'content' => '<p>Open the ForwardFi website and click <strong>Sign Up</strong>. Enter your email address and choose a password. You will receive a confirmation email; follow the link inside it to activate your account.</p>
<p>Once confirmed, log in with your email and password to reach the application dashboard.</p>',
                            ],
                            [
                                'title'   => 'Logging In and Out',
                                'content' => '<p>Visit the site and click <strong>Log In</strong>. Enter your registered email and password. To end your session, open the account menu in the top-right corner and choose <strong>Log Out</strong>.</p>
<p>If you forget your password, click <strong>Forgot password</strong> on the login page and follow the instructions sent to your email address.</p>',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Bank Accounts ─────────────────────────────────────────────────
            [
                'title' => 'Bank Accounts',
                'pages' => [
                    [
                        'title' => 'Adding and Managing Accounts',
                        'blocks' => [
                            [
                                'title'   => 'Adding a Bank Account',
                                'content' => '<p>From the dashboard, click <strong>Add Account</strong>. Fill in the following fields:</p>
<ul>
<li><strong>Account Name</strong> - A label that helps you identify this account, for example "Everyday" or "Savings".</li>
<li><strong>Account Number</strong> - Optional. Recorded for your reference only; it is not used to connect to any bank.</li>
<li><strong>Current Balance</strong> - The balance of the account right now. This is the starting point for all forecasts.</li>
<li><strong>Balance Date</strong> - The date that balance figure applies to.</li>
<li><strong>Description</strong> - Optional notes about the account.</li>
</ul>
<p>Click <strong>Save</strong> to create the account.</p>',
                            ],
                            [
                                'title'   => 'Updating Account Details',
                                'content' => '<p>Open the account you want to change and click <strong>Edit</strong>. Update any of the fields and click <strong>Save</strong>. Changing the balance or balance date will immediately affect all forecasts for that account.</p>
<p>It is good practice to update the balance regularly so that your forecasts start from an accurate figure.</p>',
                            ],
                            [
                                'title'   => 'Deleting an Account',
                                'content' => '<p>Open the account and click <strong>Delete</strong>. You will be asked to confirm. Deleting an account removes it along with all of its transaction rules, entries, and share invitations. This action cannot be undone.</p>',
                            ],
                            [
                                'title'   => 'Free Plan Account Limit',
                                'content' => '<p>On the free plan you may own one bank account. If you need more accounts, upgrade to a paid plan from your account settings. Collaborator access to accounts shared with you does not count toward your limit.</p>',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Transaction Rules ─────────────────────────────────────────────
            [
                'title' => 'Transaction Rules',
                'pages' => [
                    [
                        'title' => 'Understanding Transaction Rules',
                        'blocks' => [
                            [
                                'title'   => 'What Are Transaction Rules',
                                'content' => '<p>A transaction rule represents a single predictable item in your cash flow, such as your salary, your rent, or a subscription payment. You give it a name, a type (income or expense), and one or more scheduling entries that tell ForwardFi when it occurs and how much it is.</p>
<p>Rules do not record transactions that have already happened. They describe transactions you expect to happen so the forecast engine can project them forward.</p>',
                            ],
                            [
                                'title'   => 'Income vs Expense',
                                'content' => '<p>Set <strong>Type</strong> to <em>Income</em> for money coming in (salary, rent received, dividends) and <em>Expense</em> for money going out (mortgage, insurance, subscriptions). The forecast adds income occurrences to your balance and subtracts expense occurrences.</p>',
                            ],
                            [
                                'title'   => 'Categories and Tags',
                                'content' => '<p>You can attach categories and tags to a rule to group related items. Categories carry a colour and are useful for high-level groupings such as "Housing" or "Transport". Tags are free-form labels for finer classification. Both are optional.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Creating and Editing Rules',
                        'blocks' => [
                            [
                                'title'   => 'Creating a Rule',
                                'content' => '<p>Open a bank account and click <strong>Add Rule</strong>. Enter a name that clearly identifies the item, choose the type (income or expense), and optionally add a description, categories, and tags. Click <strong>Save</strong>. The rule is created without any entries, so you need to add at least one entry before it will appear in forecasts.</p>',
                            ],
                            [
                                'title'   => 'Editing a Rule',
                                'content' => '<p>Click the rule name to open it, then click <strong>Edit</strong>. You can change the name, type, description, categories, and tags. Saving changes to the name or type will regenerate all future occurrences for that rule.</p>',
                            ],
                            [
                                'title'   => 'Deleting a Rule',
                                'content' => '<p>Open the rule and click <strong>Delete</strong>. Confirm the prompt. All entries and future occurrences for that rule are removed. Past forecast data is unaffected.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Scheduling Entries',
                        'blocks' => [
                            [
                                'title'   => 'What Are Entries',
                                'content' => '<p>An entry defines a scheduling period for a rule. It holds the amount, the recurrence pattern, an optional start date, and an optional end date. A rule must have at least one entry to generate occurrences.</p>
<p>Having multiple entries on a single rule lets you pre-plan changes to an item without losing its history. For example, if your mortgage rate is changing in six months, you can add a second entry now with the new amount starting on that date.</p>',
                            ],
                            [
                                'title'   => 'Adding an Entry',
                                'content' => '<p>Open a rule and click <strong>Add Entry</strong>. Fill in the fields:</p>
<ul>
<li><strong>Amount</strong> - The value of each occurrence. Must be greater than zero.</li>
<li><strong>Recurrence</strong> - How often the transaction occurs. See the recurrence options below.</li>
<li><strong>Start Date</strong> - The first date from which occurrences are generated.</li>
<li><strong>End Date</strong> - Optional. Leave blank for an open-ended rule. If set, no occurrences are generated after this date.</li>
<li><strong>Description</strong> - Optional note about this period, for example "Rate fixed until June".</li>
</ul>
<p>Entries on the same rule must not have overlapping date ranges.</p>',
                            ],
                            [
                                'title'   => 'Recurrence Options',
                                'content' => '<ul>
<li><strong>Weekly</strong> - Occurs every 7 days from the start date.</li>
<li><strong>Fortnightly</strong> - Occurs every 14 days from the start date.</li>
<li><strong>Monthly</strong> - Occurs on the same day of the month as the start date.</li>
<li><strong>Day of Month</strong> - Occurs on a specific day number each month, regardless of the start date day. Set the day number in the Day of Month field (1-31).</li>
<li><strong>Quarterly</strong> - Occurs every three months from the start date.</li>
<li><strong>Yearly</strong> - Occurs once per year on the anniversary of the start date.</li>
<li><strong>One-time</strong> - Occurs exactly once on the start date.</li>
</ul>',
                            ],
                            [
                                'title'   => 'Handling Amount Changes',
                                'content' => '<p>To model a future change in a recurring amount, set an end date on the current entry and add a new entry with the new amount starting the day after. ForwardFi will use the first entry for occurrences up to its end date and the second entry from its start date onward.</p>
<p>Entries on the same rule are not allowed to overlap. If you see a validation error about overlapping date ranges, check that the end date of one entry is before the start date of the next.</p>',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Forecasting ───────────────────────────────────────────────────
            [
                'title' => 'Forecasting',
                'pages' => [
                    [
                        'title' => 'Cash Flow Forecasting',
                        'blocks' => [
                            [
                                'title'   => 'How Forecasting Works',
                                'content' => '<p>ForwardFi takes the current balance of each bank account and applies all of the future occurrences generated by the account\'s transaction rules. The result is a projected balance for every day over the next 12 months.</p>
<p>Occurrences are regenerated whenever you change a rule, add or update an entry, or update the account balance. The forecast always reflects the current state of your rules.</p>',
                            ],
                            [
                                'title'   => 'Reading the Forecast',
                                'content' => '<p>The forecast view shows your projected balance over time as a chart and a table. Income events push the balance up; expense events push it down.</p>
<p>If the projected balance drops below zero at any point, that date will be highlighted so you can identify the problem early and adjust your rules or plans accordingly.</p>',
                            ],
                            [
                                'title'   => 'Keeping Forecasts Accurate',
                                'content' => '<p>A forecast is only as good as the data behind it. To keep it useful:</p>
<ul>
<li>Update your account balance regularly to reflect actual figures.</li>
<li>End-date rules that no longer apply and add new rules for replacements.</li>
<li>Use the Description field on entries to note any assumptions you have made.</li>
</ul>',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Sharing ───────────────────────────────────────────────────────
            [
                'title' => 'Sharing and Collaboration',
                'pages' => [
                    [
                        'title' => 'Sharing an Account',
                        'blocks' => [
                            [
                                'title'   => 'Inviting a Collaborator',
                                'content' => '<p>Open the account you want to share and click <strong>Share</strong>. Enter the email address of the person you are inviting and click <strong>Send Invitation</strong>. They will receive an email with a link to accept or decline.</p>
<p>While the invitation is pending, you will see it listed with a Pending status. You can revoke a pending invitation at any time by deleting it from the shares list.</p>',
                            ],
                            [
                                'title'   => 'Accepting an Invitation',
                                'content' => '<p>If someone shares an account with you, you will receive an email containing an acceptance link. Click the link and log in (or create an account if you do not have one). The shared account will then appear in your dashboard alongside your own accounts.</p>',
                            ],
                            [
                                'title'   => 'Declining an Invitation',
                                'content' => '<p>If you do not want access to the shared account, click the decline link in the invitation email. The invitation will be marked as Declined and you will not have access to the account.</p>',
                            ],
                            [
                                'title'   => 'Collaborator Permissions',
                                'content' => '<p>Collaborators can view and edit the account, its rules, and its entries. Only the account owner can delete the account or manage share invitations.</p>',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Subscription ──────────────────────────────────────────────────
            [
                'title' => 'Plans and Subscription',
                'pages' => [
                    [
                        'title' => 'Understanding Your Plan',
                        'blocks' => [
                            [
                                'title'   => 'Free Plan',
                                'content' => '<p>The free plan lets you create one bank account with unlimited transaction rules and entries. Collaborative access to accounts shared with you is included at no cost regardless of plan.</p>',
                            ],
                            [
                                'title'   => 'Paid Plan',
                                'content' => '<p>A paid subscription removes the one-account limit, letting you create as many bank accounts as you need. Open your account settings and follow the upgrade prompts to subscribe.</p>',
                            ],
                            [
                                'title'   => 'Managing Your Subscription',
                                'content' => '<p>You can view, change, or cancel your subscription from the account settings page. If you cancel, your account reverts to the free plan at the end of the current billing period. Any accounts beyond the first are not deleted, but you will not be able to create new accounts until you are back within the free-plan limit.</p>',
                            ],
                        ],
                    ],
                ],
            ],

        ];
    }

    // -------------------------------------------------------------------------
    // Utilities
    // -------------------------------------------------------------------------

    private function slug(string $title): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($title)));
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    private function log(string $msg): void
    {
        echo $msg . PHP_EOL;
    }
}
