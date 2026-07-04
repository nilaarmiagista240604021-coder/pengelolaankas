<x-layouts::app :title="__('Dashboard')">

    <div class="space-y-6">

        <!-- Header -->
        <div class="rounded-2xl bg-pink-600 p-8 shadow-lg">
            <h1 class="text-3xl font-bold text-white">
                DASHBOARD PENGELOLAAN KAS ORGANISASI MAHASISWA
            </h1>

            <p class="mt-4 text-pink-100">
                SELAMAT DATANG DI DASHBOARD PENGELOLAAN KAS ORGANISASI MAHASISWA.
                SILAHKAN GUNAKAN FITUR-FITUR YANG TERSEDIA UNTUK MENGELOLA KAS
                ORGANISASI ANDA DENGAN LEBIH BAIK. TERIMA KASIH.
            </p>

        </div>

        <!-- Card Statistik -->
        <div class="grid gap-6 md:grid-cols-3">

            <div class="rounded-2xl border border-pink-200 bg-white p-6 shadow">
                <h3 class="text-gray-500 font-semibold">
                    Total Category
                </h3>

                <p class="mt-4 text-4xl font-bold text-pink-600">
                    {{ $totalCategory }}
                </p>
            </div>

            <div class="rounded-2xl border border-pink-200 bg-white p-6 shadow">
                <h3 class="text-gray-500 font-semibold">
                    Total Transaction
                </h3>

                <p class="mt-4 text-4xl font-bold text-pink-600">
                    {{ $totalTransaction }}
                </p>
            </div>

            <div class="rounded-2xl border border-pink-200 bg-white p-6 shadow">
                <h3 class="text-gray-500 font-semibold">
                    Total Transaction Detail
                </h3>

                <p class="mt-4 text-4xl font-bold text-pink-600">
                    {{ $totalTransactionDetail }}
                </p>
            </div>

        </div>

                    @forelse($latestTransactions as $transaction)

                        <tr class="border-t">

                            <td class="px-6 py-4">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $transaction->transaction_date }}
                            </td>

                            <td class="px-6 py-4">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $transaction->type }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $transaction->description }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts::app>