<div>
    {{-- Success is as dangerous as failure. --}}
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
        <div>

        </div>
    </div>
    <div class="grid grid-cols-3 gap-5">
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Anggota</h2>
            <div class="grid items-center grid-cols-3 pt-3">
                <div class="text-green-500">
                    <x-lucide-user class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">100</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Total Tabungan</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-landmark class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">Rp. 1,000,000,000</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Total Tagihan</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-credit-card class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">Rp. 100,000,000</span>
                </div>
            </div>
        </div>
    </div>
</div>
