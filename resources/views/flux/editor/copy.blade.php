<flux:tooltip content="{{ __('Copy to clipboard') }}" class="contents">

    <flux:editor.button
        x-on:click="
            navigator.clipboard?.writeText(
                $el.closest('[data-flux-editor]').value
            );

            $el.setAttribute('data-copied', '');

            setTimeout(
                () => $el.removeAttribute('data-copied'),
                2000
            );
        "
    >

        <flux:icon.clipboard
            variant="outline"
            class="[[data-copied]_&]:hidden size-5!"
        />

        <flux:icon.clipboard-document-check
            variant="outline"
            class="hidden [[data-copied]_&]:block size-5!"
        />

    </flux:editor.button>

</flux:tooltip>
