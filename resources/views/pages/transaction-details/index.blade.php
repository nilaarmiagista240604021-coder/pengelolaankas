<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\TransactionDetail;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function transactionDetails()
    { 
        return TransactionDetail::paginate(10);
    }

};
?>

<div class="max-w-7xl mx-auto space-y-4 bg-pink-50 p-6 rounded-xl">

    <flux:heading size="xl" class="text-pink-600 font-bold">
        Transaction Details
    </flux:heading>

    <flux:subheading size="lg" class="text-pink-500">
        Manage your transaction details
    </flux:subheading>

    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-transaction-detail">
        <flux:button
            variant="primary"
            icon="plus"
            class="bg-pink-500 hover:bg-pink-600 text-white">
            Add Transaction Detail
        </flux:button>
    </flux:modal.trigger>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-pink-100 p-4">

        <flux:table :paginate="$this->transactionDetails">

            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">No</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Transaction ID</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Notes</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Created At</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>

                @foreach ($this->transactionDetails as $detail)

                    <flux:table.row :key="$detail->id">

                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $loop->iteration }}
                        </flux:table.cell>

                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $detail->transaction_id }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-600">
                            {{ $detail->notes ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap text-pink-500">
                            {{ $detail->created_at->diffForHumans() }}
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
                                        wire:click="edit({{ $detail->id }})">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete', {id: {{ $detail->id }}})">
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