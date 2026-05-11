<div class="relative flex min-h-[calc(100vh-64px)]">

    <%-- ── Mobile sidebar toggle ─────────────────────────────── --%>
    <button class="mx-4 mt-4 inline-flex items-center gap-2 rounded-md border border-border px-3 py-2 text-sm text-foreground hover:bg-accent md:hidden"
            id="js-help-sidebar-toggle"
            aria-expanded="false" aria-controls="js-help-sidebar" aria-label="Open navigation">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round" aria-hidden="true">
            <line x1="3" y1="6"  x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
        <span>Menu</span>
    </button>

    <%-- ── Left sidebar: global navigation ──────────────────── --%>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-border bg-background transition-transform md:sticky md:top-16 md:h-[calc(100vh-64px)] md:translate-x-0"
           id="js-help-sidebar"
           aria-label="Help documentation navigation">

        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-background px-5 py-4">
            <% if $HelpDesk %>
                <a href="$HelpDesk.Link" class="text-sm font-semibold text-primary no-underline hover:text-primary/80">$HelpDesk.Title</a>
            <% end_if %>
            <button class="inline-flex items-center rounded-sm p-1 text-muted-foreground hover:bg-accent hover:text-accent-foreground md:hidden"
                    id="js-help-sidebar-close"
                    aria-label="Close navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6"  x2="6"  y2="18" />
                    <line x1="6"  y1="6"  x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <% loop $HelpNav %>
                <div class="mb-5">
                    <span class="block px-2 pb-2 text-xs font-semibold uppercase tracking-[0.06em] text-muted-foreground">$Title</span>
                    <ul class="m-0 list-none p-0">
                        <% loop $NavPages %>
                            <li>
                                <a href="$Link"
                                   class="block rounded-md px-3 py-2 text-sm text-foreground/80 no-underline transition-colors hover:bg-accent hover:text-accent-foreground<% if $IsCurrentPage %> bg-primary/10 font-medium text-primary<% end_if %>"
                                   <% if $IsCurrentPage %>aria-current="page"<% end_if %>>
                                    $Title
                                </a>
                            </li>
                        <% end_loop %>
                    </ul>
                </div>
            <% end_loop %>
        </nav>

    </aside>

    <%-- ── Sidebar overlay (mobile) ──────────────────────────── --%>
    <div class="fixed inset-0 z-30 hidden bg-black/45 md:hidden" id="js-help-sidebar-overlay" aria-hidden="true"></div>

    <%-- ── Main content area ─────────────────────────────────── --%>
    <div class="flex-1 min-w-0 px-4 pb-16 pt-8 md:px-8">

        <div class="mx-auto max-w-[760px]">

            <%-- Breadcrumb --%>
            <nav class="mb-3" aria-label="Breadcrumb">
                <ol class="m-0 flex flex-wrap items-center gap-1 p-0 list-none">
                    <% if $HelpDesk %>
                        <li class="flex items-center gap-1">
                            <a href="$HelpDesk.Link" class="text-sm text-muted-foreground no-underline hover:text-foreground">$HelpDesk.MenuTitle</a>
                            <span class="text-sm text-muted-foreground" aria-hidden="true">/</span>
                        </li>
                    <% end_if %>
                    <% if $Parent %>
                        <li class="flex items-center gap-1">
                            <a href="$Parent.Link" class="text-sm text-muted-foreground no-underline hover:text-foreground">$Parent.MenuTitle</a>
                            <span class="text-sm text-muted-foreground" aria-hidden="true">/</span>
                        </li>
                    <% end_if %>
                    <li class="flex items-center gap-1">
                        <span class="text-sm font-medium text-foreground/90" aria-current="page">$MenuTitle</span>
                    </li>
                </ol>
            </nav>

            <h1 class="mb-6 mt-4 text-3xl font-bold text-foreground md:text-4xl">$Title</h1>

            <div class="mb-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
                <span>Last updated: $LastEdited.Nice</span>
                <% if $AuthorName %><span>Author: $AuthorName</span><% end_if %>
                <span>$ReadingTimeLabel</span>
                <% if $ArticleStatus %><span>Status: $ArticleStatus</span><% end_if %>
            </div>

            <% if $TopicItems %>
                <div class="mb-8 flex flex-wrap gap-2" aria-label="Topics">
                    <% loop $TopicItems %>
                        <span class="inline-flex rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">$Title</span>
                    <% end_loop %>
                </div>
            <% end_if %>

            <%-- On-this-page table of contents --%>
            <% if $HelpContentBlocks %>
                <nav class="mb-8 rounded-lg border border-border bg-muted/40 px-5 py-4" aria-label="On this page">
                    <h4 class="mb-3 mt-0 text-xs font-semibold uppercase tracking-[0.06em] text-muted-foreground">On this page</h4>
                    <ul class="m-0 flex list-none flex-col gap-1 p-0">
                        <% loop $HelpContentBlocks %>
                            <li>
                                <a href="#$Anchor"
                                   data-help-toc-link
                                   class="block rounded-sm px-2 py-1 text-sm text-foreground/80 no-underline transition-colors hover:bg-accent hover:text-accent-foreground">
                                    $Title
                                </a>
                            </li>
                        <% end_loop %>
                    </ul>
                </nav>
            <% end_if %>

            <%-- Content blocks --%>
            <div>
                $ElementalArea
            </div>

            <% if $RelatedHelpPages %>
                <section class="mt-10 rounded-lg border border-border p-5" aria-label="Related articles">
                    <h3 class="mb-3 mt-0 text-base font-semibold text-foreground">Related articles</h3>
                    <ul class="m-0 list-disc pl-5">
                        <% loop $RelatedHelpPages %>
                            <li><a class="text-primary hover:underline" href="$Link">$Title</a></li>
                        <% end_loop %>
                    </ul>
                </section>
            <% end_if %>

            <section class="mt-10 rounded-lg border border-border p-5" aria-label="Article feedback">
                <h3 class="mb-2 mt-0 text-base font-semibold text-foreground">Was this helpful?</h3>
                <form id="js-help-feedback-form" method="post" action="$Link(feedback)">
                    <input type="hidden" name="SecurityID" value="$SecurityID" />
                    <div class="mb-3 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="Helpful" value="yes" required />
                            Yes
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="Helpful" value="no" required />
                            No
                        </label>
                    </div>
                    <label for="js-help-feedback-comment" class="mb-1 block text-sm text-muted-foreground">Optional comment</label>
                    <textarea id="js-help-feedback-comment" name="Comment" rows="3" maxlength="2000" class="mb-3 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"></textarea>
                    <button type="submit" class="inline-flex items-center rounded-md border border-border px-3 py-2 text-sm hover:bg-accent">Submit feedback</button>
                    <p id="js-help-feedback-message" class="mt-3 text-sm" aria-live="polite"></p>
                </form>
            </section>

            <%-- Prev / Next page navigation --%>
            <nav class="mt-10 flex flex-col gap-4 border-t border-border pt-8 md:flex-row" aria-label="Page navigation">
                <% if $PreviousHelpPage %>
                    <a href="$PreviousHelpPage.Link" class="flex flex-1 flex-col gap-1 rounded-lg border border-border px-5 py-4 text-foreground/90 no-underline transition-all hover:border-primary hover:shadow-sm">
                        <span class="text-xs font-medium uppercase tracking-[0.06em] text-muted-foreground">Previous</span>
                        <strong class="text-sm font-semibold text-foreground">$PreviousHelpPage.Title</strong>
                    </a>
                <% end_if %>
                <% if $NextHelpPage %>
                    <a href="$NextHelpPage.Link" class="flex flex-1 flex-col gap-1 rounded-lg border border-border px-5 py-4 text-right text-foreground/90 no-underline transition-all hover:border-primary hover:shadow-sm">
                        <span class="text-xs font-medium uppercase tracking-[0.06em] text-muted-foreground">Next</span>
                        <strong class="text-sm font-semibold text-foreground">$NextHelpPage.Title</strong>
                    </a>
                <% end_if %>
            </nav>

        </div>
    </div>

</div>

<script>
(function () {
    var toggle  = document.getElementById('js-help-sidebar-toggle');
    var close   = document.getElementById('js-help-sidebar-close');
    var sidebar = document.getElementById('js-help-sidebar');
    var overlay = document.getElementById('js-help-sidebar-overlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        if (close) {
            close.focus();
        }
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        if (toggle) {
            toggle.focus();
        }
    }

    if (toggle)  toggle.addEventListener('click', openSidebar);
    if (close)   close.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
            closeSidebar();
        }
    });

    // Highlight the active ToC link as the user scrolls
    var tocLinks = document.querySelectorAll('[data-help-toc-link]');
    if (tocLinks.length && 'IntersectionObserver' in window) {
        var headings = document.querySelectorAll('.help-block');
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var id = entry.target.id;
                tocLinks.forEach(function (link) {
                    var isActive = link.getAttribute('href') === '#' + id;
                    link.classList.toggle('bg-primary/10', isActive);
                    link.classList.toggle('font-medium', isActive);
                    link.classList.toggle('text-primary', isActive);
                });
            });
        }, { rootMargin: '0px 0px -70% 0px' });

        headings.forEach(function (h) { observer.observe(h); });
    }

    if (typeof window.gtag === 'function') {
        window.gtag('event', 'help_article_view', {
            article_title: '$Title.JS',
            article_status: '$ArticleStatus.JS',
            article_url: window.location.pathname
        });

        var params = new URLSearchParams(window.location.search);
        var query = params.get('q') || params.get('query') || params.get('search');
        if (query) {
            window.gtag('event', 'help_search', { search_term: query });
        }

        document.querySelectorAll('form[data-help-search-form]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var input = form.querySelector('input[name="q"], input[name="query"], input[name="search"]');
                if (input && input.value) {
                    window.gtag('event', 'help_search', { search_term: input.value });
                }
            });
        });
    }

    var feedbackForm = document.getElementById('js-help-feedback-form');
    var feedbackMessage = document.getElementById('js-help-feedback-message');
    if (feedbackForm && window.fetch) {
        feedbackForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(feedbackForm);
            fetch(feedbackForm.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            }).then(function (data) {
                feedbackMessage.textContent = 'Thanks for your feedback.';
                if (typeof window.gtag === 'function') {
                    window.gtag('event', 'help_feedback_submitted', {
                        helpful: data.helpful || '',
                        article_title: '$Title.JS'
                    });
                }
                feedbackForm.reset();
            }).catch(function () {
                feedbackMessage.textContent = 'Sorry, feedback could not be submitted.';
            });
        });
    }
}());
</script>
