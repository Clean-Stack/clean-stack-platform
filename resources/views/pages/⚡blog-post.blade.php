<?php

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

new class extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        abort_unless(
            $post->status === 'published' &&
            $post->published_at !== null &&
            $post->published_at->isPast(),
            404
        );

        $this->post = $post->load([
            'category',
            'author',
        ]);
    }

    public function render()
    {
        $postTitle = $this->post->title . ' — Clean Stack';

        $postDescription = $this->post->excerpt;

        $postUrl = route('blog.show', $this->post);

        $postImage = $this->post->featured_image
            ?? $this->post->featured_image_medium
            ?? $this->post->featured_image_thumbnail;

        $postImageUrl = $postImage
            ? url(Storage::url($postImage))
            : null;

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->post->title,
            'description' => $this->post->excerpt,
            'url' => $postUrl,
            'datePublished' => $this->post->published_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->post->author->name,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Clean Stack',
            ],
        ];

        if ($postImageUrl) {
            $articleSchema['image'] = [
                $postImageUrl,
            ];
        }

        return $this->view()
            ->layout('layouts::public', [
                'metaDescription' => $postDescription,

                'robots' => 'index, follow',
                'canonicalUrl' => $postUrl,
                'ogTitle' => $postTitle,
                'ogDescription' => $postDescription,
                'ogType' => 'article',
                'ogUrl' => $postUrl,
                'ogImage' => $postImageUrl,

                'twitterTitle' => $postTitle,
                'twitterDescription' => $postDescription,
                'twitterImage' => $postImageUrl,
                'articleSchema' => $articleSchema,
            ])
            ->title($postTitle);
    }
};
?>

<article class="mx-auto max-w-3xl px-6 py-16 lg:px-8">

    {{-- Back to Blog --}}
    <flux:button
        href="{{ route('blog') }}"
        variant="subtle"
        size="sm"
        wire:navigate
    >
        ← Back to Blog
    </flux:button>


    {{-- Post Header --}}
    <header class="mt-8">

        <flux:badge size="sm">
            {{ $post->category->name }}
        </flux:badge>

        <flux:heading
            level="1"
            size="xl"
            class="mt-4"
        >
            {{ $post->title }}
        </flux:heading>

        <flux:text
            size="sm"
            class="mt-4"
        >
            {{ $post->published_at->format('F d, Y') }}
        </flux:text>

        <flux:text class="mt-6 text-lg">
            {{ $post->excerpt }}
        </flux:text>

    </header>


    {{-- Featured Image --}}
    @if (
        $post->featured_image ||
        $post->featured_image_medium ||
        $post->featured_image_thumbnail
    )

        <figure class="mt-10 overflow-hidden rounded-xl">

            <img
                src="{{ Storage::url(
                    $post->featured_image
                        ?? $post->featured_image_medium
                        ?? $post->featured_image_thumbnail
                ) }}"
                @if (
                    $post->featured_image &&
                    $post->featured_image_medium &&
                    $post->featured_image !== $post->featured_image_medium
                )
                    srcset="
                        {{ Storage::url($post->featured_image_medium) }} 800w,
                        {{ Storage::url($post->featured_image) }} 1200w
                    "
                @endif
                sizes="(min-width: 768px) 768px, 100vw"
                alt="{{ $post->title }}"
                decoding="async"
                fetchpriority="high"
                class="h-auto w-full object-cover"
            >

        </figure>

    @endif


    {{-- Post Content --}}
    <div class="mt-10">

        <flux:text class="whitespace-pre-line leading-7">
            {!! $post->content !!}
        </flux:text>

    </div>

</article>
