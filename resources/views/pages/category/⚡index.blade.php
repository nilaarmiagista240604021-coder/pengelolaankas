<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On; 
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component
{
    use WithPagination;

    // Properti untuk menangani Form Tambah / Edit Kategori
    public $categoryId; 
    public $name = '';
    public $type = 'Pemasukan';
    public $description = '';

    #[Computed]
    public function categories()
    {
        return Category::paginate(10);
    }

    // 1. Fungsi Simpan (Tambah Baru)
    public function store()
    {
        $this->validate([
            'name' => 'required|min:2',
            'type' => 'required|in:Pemasukan,Pengeluaran',
            'description' => 'nullable',
        ]);

        Category::create([
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'description']);
        $this->type = 'Pemasukan';

        $this->js("Flux.modal('tambah-kategori-modal').close()");
    }

    // 2. Fungsi Edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->description = $category->description;

        $this->js("Flux.modal('tambah-kategori-modal').show()");
    }

    // 3. Fungsi Hapus
    #[On('confirm-delete')]
    public function delete($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
        }
    }
};

?>

<div> 
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Kas Organisasi Mahasiswa</h2>
            <p class="text-gray-500">Kelola kategori pemasukan dan pengeluaran...</p>
        </div>

        <flux:modal.trigger name="tambah-kategori-modal">
            <button class="bg-pink-500 text-white px-4 py-2 rounded">
                + Tambah Kategori
            </button>
        </flux:modal.trigger>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
        <table></table>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow-md border border-pink-100 p-4">

        <flux:table :paginate="$this->categories">

            <flux:table.columns>
                <flux:table.column class="text-pink-600 font-semibold">No</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">ID</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Name</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Type</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Description</flux:table.column>
                <flux:table.column class="text-pink-600 font-semibold">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $loop->iteration }}
                        </flux:table.cell>

                        <flux:table.cell class="text-pink-700">
                            {{ $category->id }}
                        </flux:table.cell>

                        <flux:table.cell class="text-pink-700 font-medium">
                            {{ $category->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($category->type == 'Pemasukan')
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-sm">
                                    {{ $category->type }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-sm">
                                    {{ $category->type }}
                                </span>
                            @endif
                        </flux:table.cell>

                       <flux:table.cell class="text-zinc-600 max-w-xs whitespace-normal break-words">
                                    {{ $category->description ?? '-' }}
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
                                        wire:click="edit({{ $category->id }})">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
                                        wire:click="$dispatch('confirm-delete', { id: {{ $category->id }} })">
                                        Hapus
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <flux:modal name="tambah-kategori-modal" class="md:w-[24rem]">
            <div>
                <h3 class="text-lg font-bold mb-4">Tambah Kategori Baru</h3>
                
                <form wire:submit.prevent="store">
                     <div class="mb-3">
                        <label class="block text-sm mb-1">ID</label>
                        <input type="text" wire:model="id" class="w-full border rounded p-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Nama Kategori</label>
                        <input type="text" wire:model="name" class="w-full border rounded p-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Type</label>
                        <select wire:model="type" class="w-full border rounded p-2 bg-white">
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm mb-1">Deskripsi</label>
                        <textarea wire:model="description" class="w-full border rounded p-2" rows="3"></textarea>
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