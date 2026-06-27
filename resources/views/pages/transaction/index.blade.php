<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Transaction;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function transactions()
    {
        return Transaction::paginate(10);
    }

};
?>

<div class="max-w-7xl mx-auto space-y-4 min-h-screen bg-gradient-to-br from-pink-100 via-pink-50 to-pink-200 p-6 rounded-xl">

    <flux:heading size="xl" class="text-pink-600 font-bold">
        Transaction
    </flux:heading>

    <flux:subheading size="lg" class="text-pink-500">
        Manage your transactions
    </flux:subheading>

    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-transaction">
        <flux:button
            variant="primary"
            icon="plus"
            class="bg-pink-500 hover:bg-pink-600 text-white">
            Add Transaction
        </flux:button>
    </flux:modal.trigger>

    {{-- table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-pink-100 p-4">

        <flux:table :paginate="$this->transactions">

            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">ID</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">User ID</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Category ID</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Transaction Date</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Amount</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Type</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Description</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->transactions as $transaction)

                    <flux:table.row :key="$transaction->id">

                        <flux:table.cell>
                            {{ $transaction->id }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $transaction->user_id }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $transaction->category_id }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap text-pink-700">
                            {{ $transaction->transaction_date }}
                        </flux:table.cell>

                        <flux:table.cell class="text-pink-700 font-semibold">
                            {{ $transaction->amount }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ ucfirst($transaction->type) }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $transaction->description ?? '-' }}
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

                                    <flux:menu.item
                                        icon="pencil"
                                        wire:click="edit({{ $transaction->id }})">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete', {id: {{ $transaction->id }}})">
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