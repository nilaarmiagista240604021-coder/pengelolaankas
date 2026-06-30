<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On; 
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Category;

new class extends Component
{
    use WithPagination;

    // Properti untuk menangani Form Tambah / Edit Transaksi
    public $transactionId; 
    public $category_id = '';
    public $transaction_date = '';
    public $amount = '';
    public $type = 'Pemasukan';
    public $description = '';

    // Menginisialisasi tanggal hari ini secara default
    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
    }

    #[Computed]
    public function transactions()
    {
        // Mengambil data transaksi beserta relasi kategorinya
        return Transaction::with('category')->latest('transaction_date')->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        // Diperlukan untuk pilihan dropdown kategori di dalam form modal
        return Category::all();
    }

    // 1. Fungsi Simpan (Tambah Baru / Update)
    public function store()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:Pemasukan,Pengeluaran',
            'description' => 'nullable|string',
        ]);

        // Berdasarkan skema di "Screenshot 2026-06-20 204132.png", menyertakan user_id autentikasi aktif
        Transaction::updateOrCreate(
            ['id' => $this->transactionId],
            [
                'user_id' => auth()->id() ?? 1, // Default ke 1 jika tidak menggunakan sistem auth laravel
                'category_id' => $this->category_id,
                'transaction_date' => $this->transaction_date,
                'amount' => $this->amount,
                'type' => $this->type,
                'description' => $this->description,
            ]
        );

        $this->reset(['transactionId', 'category_id', 'amount', 'description']);
        $this->transaction_date = date('Y-m-d');
        $this->type = 'Pemasukan';

        $this->js("Flux.modal('tambah-transaksi-modal').close()");
    }

    // 2. Fungsi Edit
    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $this->transactionId = $transaction->id;
        $this->category_id = $transaction->category_id;
        $this->transaction_date = $transaction->transaction_date;
        $this->amount = $transaction->amount;
        $this->type = $transaction->type;
        $this->description = $transaction->description;

        $this->js("Flux.modal('tambah-transaksi-modal').show()");
    }

    // 3. Fungsi Hapus
    #[On('confirm-delete-transaction')]
    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
        }
    }
};

?>

<div> 
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Transaksi Kas Organisasi</h2>
            <p class="text-gray-500">Kelola riwayat pemasukan dan pengeluaran keuangan...</p>
        </div>

        <flux:modal.trigger name="tambah-transaksi-modal">
            <button class="bg-pink-500 text-white px-4 py-2 rounded">
                + Tambah Transaksi
            </button>
        </flux:modal.trigger>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-pink-100 p-4">

        <flux:table :paginate="$this->transactions">

            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">No</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Tanggal</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Kategori</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Jenis</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Jumlah</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Keterangan</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->transactions as $transaction)
                    <flux:table.row :key="$transaction->id">
                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $loop->iteration }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-700">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $transaction->category->name ?? 'Tanpa Kategori' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($transaction->type == 'Pemasukan')
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-sm">
                                    {{ $transaction->type }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-sm">
                                    {{ $transaction->type }}
                                </span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="font-bold {{ $transaction->type == 'Pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-600 max-w-xs whitespace-normal break-words">
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
                                        wire:click="$dispatch('confirm-delete-transaction', { id: {{ $transaction->id }} })">
                                        Hapus
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        {{-- Modal Form Tambah / Edit Transaksi --}}
        <flux:modal name="tambah-transaksi-modal" class="md:w-[24rem]">
            <div>
                <h3 class="text-lg font-bold mb-4">{{ $transactionId ? 'Edit Transaksi' : 'Tambah Transaksi Baru' }}</h3>
                
                <form wire:submit.prevent="store">
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Tanggal Transaksi</label>
                        <input type="date" wire:model="transaction_date" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm mb-1">Kategori</label>
                        <select wire:model="category_id" class="w-full border rounded p-2 bg-white" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm mb-1">Jenis Transaksi</label>
                        <select wire:model="type" class="w-full border rounded p-2 bg-white">
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm mb-1">Jumlah (Rp)</label>
                        <input type="number" wire:model="amount" min="0" class="w-full border rounded p-2" placeholder="Contoh: 50000" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm mb-1">Deskripsi / Keterangan</label>
                        <textarea wire:model="description" class="w-full border rounded p-2" rows="3" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-4">
                        <flux:modal.close>
                            <button type="button" class="bg-gray-300 px-3 py-1 rounded">Batal</button>
                        </flux:modal.close>
                        <button type="submit" class="bg-pink-500 text-white px-3 py-1 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>
</div>