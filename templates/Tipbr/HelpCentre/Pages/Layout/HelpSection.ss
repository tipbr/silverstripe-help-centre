<div class="px-4 pb-16 pt-10 md:px-8">
    <div class="mx-auto max-w-[860px]">
        <nav class="mb-3" aria-label="Breadcrumb">
            <ol class="m-0 flex flex-wrap items-center gap-1 p-0 list-none">
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

        <h1 class="mb-4 mt-0 text-3xl font-bold text-foreground md:text-4xl">$Title</h1>

        <% if $Content %>
            <div class="mb-8 text-foreground/90 leading-relaxed">
                $Content
            </div>
        <% end_if %>

        <section aria-label="Section articles">
            <h2 class="mb-3 mt-0 text-lg font-semibold text-foreground">Articles</h2>
            <% if $Children %>
                <ul class="m-0 list-disc pl-5">
                    <% loop $Children.Sort(Sort) %>
                        <li><a class="text-primary hover:underline" href="$Link">$MenuTitle</a></li>
                    <% end_loop %>
                </ul>
            <% else %>
                <p class="text-sm text-muted-foreground">No articles in this section yet.</p>
            <% end_if %>
        </section>
    </div>
</div>

<script>
(function () {
    if (typeof window.gtag !== 'function') return;

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
}());
</script>
