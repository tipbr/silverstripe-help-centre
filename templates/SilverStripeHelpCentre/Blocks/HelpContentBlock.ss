<section class="help-block mb-2 border-b border-border pb-8 last:border-b-0" id="$Anchor.ATT">
    <h2 class="group mb-4 flex items-center gap-2 text-2xl font-semibold text-foreground">
        <span aria-hidden="true" class="h-0 w-0 overflow-hidden"></span>
        $Title
        <a href="#$Anchor.ATT" class="shrink-0 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100 hover:text-primary" aria-label="Link to $Title.ATT">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
        </a>
    </h2>
    <% if $Content %>
        <div class="text-foreground/90 leading-relaxed [&_a]:text-primary [&_a:hover]:underline [&_blockquote]:my-4 [&_blockquote]:border-l-4 [&_blockquote]:border-primary [&_blockquote]:pl-4 [&_code]:rounded-sm [&_code]:bg-muted [&_code]:px-1.5 [&_code]:py-0.5 [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:text-lg [&_h4]:mt-6 [&_h4]:mb-2 [&_h4]:text-base [&_ol]:mb-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-4 [&_pre]:mb-4 [&_pre]:overflow-x-auto [&_pre]:rounded-md [&_pre]:bg-muted [&_pre]:p-4 [&_pre]:text-foreground [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-6">
            $Content
        </div>
    <% end_if %>
</section>
