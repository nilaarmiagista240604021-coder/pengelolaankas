<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component 
{
    use WithPagination;

    #[Computed]
    public function categories()
    {
        return Category::paginate(1);
    }

};
?>

<div class="max-w-7xl mx-auto space-y-4 min-h-screen bg-gradient-to-br from-pink-100 via-pink-50 to-pink-200 p-6 rounded-xl">
    <flux:heading size="xl" class="text-pink-600 font-bold">Category</flux:heading>

    <flux:subheading size="lg" class="text-pink-500">
        Manage your categories
    </flux:subheading>

    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-category">
        <flux:button
            variant="primary"
            icon="plus"
            class="bg-pink-500 hover:bg-pink-600 text-white">
            Add Category
        </flux:button>
    </flux:modal.trigger>

    {{-- table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-pink-100 p-4">

       <flux:table :paginate="$this->categories">
            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">No</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Name</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Type</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Description</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Created At</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->categories as $category)
                    <flux:table.row :key="$category->id">

                        <flux:table.cell class="flex items-center gap-3 text-pink-700 font-medium">
                            {{ $category->name }}
                        </flux:table.cell>

                         <flux:table.cell class="flex items-center gap-3 text-pink-700 font-medium">
                            {{ $category->type }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $category->description ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap text-pink-500">
                            {{ $category->created_at->diffForHumans() }}
                        </flux:table.cell>

                        <flux:table.cell>

                            <flux:dropdown>
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="ellipsis-horizontal"
                                    inset="top bottom"
                                    class="text-pink-600">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $category->id }})">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete', {id: {{ $category->id }}})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>

                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

    </div>

</div>