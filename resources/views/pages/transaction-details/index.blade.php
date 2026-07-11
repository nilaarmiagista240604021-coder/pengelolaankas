<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On; 
use Livewire\WithPagination;
use App\Models\TransactionDetail;
use App\Models\Transaction; 

new class extends Component
{
    use WithPagination;

    public $transactionDetailId; 
    public $transactionId; 
    public $notes = '';

   
    public function mount($transactionId = null)
    {
        $this->transactionId = $transactionId ?? Transaction::latest()->value('id');
    }

    #[Computed]
    public function transactions()
    {
        return TransactionDetail::where('transaction_id', $this->transactionId)
            ->latest()
            ->paginate(10);
    }

   
    public function save()
    {
        $this->validate([
            'notes' => 'required|min:3',
        ]);

        if (!$this->transactionId) {
            session()->flash('error', 'Gagal menyimpan! Anda harus membuat data Transaksi Utama terlebih dahulu di tabel induk.');
            return;
        }

        TransactionDetail::updateOrCreate(
            ['id' => $this->transactionDetailId],
            [
                'transaction_id' => $this->transactionId, 
                'notes'          => $this->notes,
            ]
        );

        $this->reset(['transactionDetailId', 'notes']);
        $this->js("Flux.modal('transaction-modal').close()");
    }

 
    public function edit($id)
    {
        $detail = TransactionDetail::findOrFail($id);
        
        $this->transactionDetailId = $detail->id;
        $this->transactionId       = $detail->transaction_id;
        $this->notes               = $detail->notes;

        $this->js("Flux.modal('transaction-modal').show()");
    }

    public function openCreateModal()
    {
        $this->reset(['transactionDetailId', 'notes']);
        $this->js("Flux.modal('transaction-modal').show()");
    }

    #[On('confirm-delete-transaction')]
    public function delete($id)
    {
        $detail = TransactionDetail::find($id);
        if ($detail) {
            $detail->delete();
        }
    }
};

?>

<div> 
    {{-- Info Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-zinc-800">Transaction Details</h2>
            @if($transactionId)
                <p class="text-gray-500">Mengelola catatan untuk Transaksi: <span class="font-mono text-pink-600 font-bold"></span></p>
            @else
                <p class="text-red-500 font-semibold">Peringatan: Belum ada Transaksi Utama yang terdeteksi di database!</p>
            @endif
        </div>

        <button wire:click="openCreateModal" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded transition-colors" @disabled(!$transactionId)>
            + Tambah Catatan
        </button>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table Section --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-pink-100 p-4">

        <flux:table :paginate="$this->transactions">

            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">No</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Notes / Catatan</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Created At</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->transactions as $index => $transaction)
                    <flux:table.row :key="$transaction->id">
                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $this->transactions->firstItem() + $index }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-600 max-w-xs whitespace-normal break-words">
                            {{ $transaction->notes ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 text-sm">
                            {{ $transaction->created_at->format('d M Y, H:i') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" class="text-pink-600"></flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="pencil" wire:click="edit({{ $transaction->id }})">Edit</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete-transaction', { id: {{ $transaction->id }} })">Hapus</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-zinc-400 py-4">
                            Belum ada catatan untuk transaksi ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Modal Form --}}
        <flux:modal name="transaction-modal" class="md:w-[26rem]">
            <div>
                <h3 class="text-lg font-bold mb-4 text-zinc-800">
                    {{ $transactionDetailId ? 'Ubah Catatan' : 'Tambah Catatan Baru' }}
                </h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Notes / Catatan</flux:label>
                        <flux:textarea 
                            wire:model.live="notes" 
                            rows="4" 
                            placeholder="Tulis catatan transaksi di sini..." 
                        />
                        <flux:error name="notes" />
                    </flux:field>
                    
                    <div class="flex justify-end gap-2 mt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost">Batal</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white border-none">
                            Simpan
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>
</div>