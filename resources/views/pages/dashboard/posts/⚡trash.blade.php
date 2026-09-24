<?php

use App\Models\Post;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\PostImageManager;
use Illuminate\Support\Facades\DB;

new
#[Layout('layouts::app')]
#[Title('Trash')]
class extends Component {
    public ?int $postToRestore = null;

    public ?int $postToForceDelete = null;

    public bool $showRestoreModal = false;

    public bool $showForceDeleteModal = false;

    public function confirmRestore(int $postId): void
    {
        $this->postToRestore = $postId;

        $this->showRestoreModal = true;
    }

    public function restorePost(): void
    {
        if ($this->postToRestore === null) {
            return;
        }

        Post::onlyTrashed()
            ->findOrFail($this->postToRestore)
            ->restore();

        $this->postToRestore = null;

        $this->showRestoreModal = false;

        Flux::toast(
            heading: 'Post restored',
            text: 'The post has been restored successfully.',
            variant: 'success',
        );
    }

    public function confirmForceDelete(int $postId): void
    {
        $this->postToForceDelete = $postId;

        $this->showForceDeleteModal = true;
    }


    public function forceDeletePost(): void
    {
        if ($this->postToForceDelete === null) {
            return;
        }

        $post = Post::onlyTrashed()
            ->findOrFail($this->postToForceDelete);

        DB::transaction(function () use ($post): void {
            $post->forceDelete();
        });

        app(PostImageManager::class)->deleteVariants($post);

        $this->postToForceDelete = null;
        $this->showForceDeleteModal = false;

        Flux::toast(
            heading: 'Post permanently deleted',
            text: 'The post has been permanently deleted.',
            variant: 'success',
        );
    }

    public function with(): array
    {
        return [
            'posts' => Post::onlyTrashed()
                ->with('category')
                ->latest('deleted_at')
                ->get(),
        ];
    }
};
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    {{-- Header --}}
    <div>

        <flux:button
            href="{{ route('dashboard.posts.index') }}"
            variant="subtle"
            size="sm"
            wire:navigate
        >
            ← Back to Posts
        </flux:button>

        <flux:heading size="xl" class="mt-4">
            Trash
        </flux:heading>

        <flux:text class="mt-1">
            Posts that have been moved to the trash.
        </flux:text>

    </div>


    {{-- Posts --}}
    <div class="mt-6">

        <flux:card>

            <flux:table>

                <flux:table.columns>

                    <flux:table.column>
                        Title
                    </flux:table.column>

                    <flux:table.column>
                        Category
                    </flux:table.column>

                    <flux:table.column>
                        Status
                    </flux:table.column>

                    <flux:table.column>
                        Deleted
                    </flux:table.column>

                    <flux:table.column>
                        Actions
                    </flux:table.column>

                </flux:table.columns>


                <flux:table.rows>

                    @forelse ($posts as $post)

                        <flux:table.row
                            wire:key="trashed-post-{{ $post->id }}"
                        >

                            <flux:table.cell>
                                {{ $post->title }}
                            </flux:table.cell>


                            <flux:table.cell>
                                {{ $post->category->name }}
                            </flux:table.cell>


                            <flux:table.cell>

                                <flux:badge size="sm">
                                    {{ ucfirst($post->status) }}
                                </flux:badge>

                            </flux:table.cell>


                            <flux:table.cell>

                                {{ $post->deleted_at->format('M d, Y H:i') }}

                            </flux:table.cell>


                            <flux:table.cell>

                                <div class="flex items-center gap-2">

                                    <flux:button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        wire:click="confirmRestore({{ $post->id }})"
                                    >
                                        Restore
                                    </flux:button>

                                    <flux:button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        wire:click="confirmForceDelete({{ $post->id }})"
                                    >
                                        Delete Permanently
                                    </flux:button>

                                </div>

                            </flux:table.cell>

                        </flux:table.row>

                    @empty

                        <flux:table.row>

                            <flux:table.cell colspan="5">

                                <div class="py-10 text-center">

                                    <flux:heading size="sm">
                                        Trash is empty
                                    </flux:heading>

                                    <flux:text size="sm" class="mt-1">
                                        Deleted posts will appear here.
                                    </flux:text>

                                </div>

                            </flux:table.cell>

                        </flux:table.row>

                    @endforelse

                </flux:table.rows>

            </flux:table>

        </flux:card>

    </div>
    <flux:modal
        wire:model.self="showRestoreModal"
        class="min-w-[22rem]"
    >
        <div class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Restore Post?
                </flux:heading>

                <flux:text class="mt-2">
                    This post will be restored and returned to the posts list.
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">

                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="outline"
                    wire:click="restorePost"
                    wire:loading.attr="disabled"
                    wire:target="restorePost"
                >
                <span
                    wire:loading.remove
                    wire:target="restorePost"
                >
                    Restore Post
                </span>

                    <span
                        wire:loading
                        wire:target="restorePost"
                    >
                    Restoring...
                </span>
                </flux:button>

            </div>

        </div>
    </flux:modal>

    <flux:modal
        wire:model.self="showForceDeleteModal"
        class="min-w-[22rem]"
    >
        <div class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Delete Permanently?
                </flux:heading>

                <flux:text class="mt-2">
                    This action will permanently remove the post and cannot be undone.
                </flux:text>
            </div>

            <div class="flex justify-end gap-2">

                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button
                    type="button"
                    variant="outline"
                    wire:click="forceDeletePost"
                    wire:loading.attr="disabled"
                    wire:target="forceDeletePost"
                >
                <span
                    wire:loading.remove
                    wire:target="forceDeletePost"
                >
                    Delete Permanently
                </span>

                    <span
                        wire:loading
                        wire:target="forceDeletePost"
                    >
                    Deleting...
                </span>
                </flux:button>

            </div>

        </div>
    </flux:modal>
</div>
