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
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Anggota Aktif</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-user-check class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->anggotaCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Anggota Belum Di Approve</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-user-pen class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->anggotaUnregisteredCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah User Anggota Mobile</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-tablet-smartphone class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->userMobileAnggotaCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg md:col-span-3 shadow-slate-300/50">
            <h2 class="font-bold">Grafik Pinjaman Aktif</h2>
            <div
                x-data="{
                    chart: null,
                    init() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @js($chartData['labels']),
                                datasets: [{
                                    label: 'Jumlah Pinjaman',
                                    data: @js($chartData['data']),
                                    backgroundColor: '#4F46E5',
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                // Hapus atau jangan pakai indexAxis: 'y'
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Pinjaman Aktif Tiap Bulan Tahun {{ now()->year }}'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1 }
                                    }
                                }
                            }
                        });
                    }
                }"
                x-init="init()"
                class="w-full p-4 bg-white rounded shadow"
            >
                <canvas x-ref="canvas" class="h-[200px] md:h-[300px] w-full"></canvas>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Pinjaman Pending</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-circle-dashed class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->pinjamanPendingCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Pinjaman Aktif</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-square-check-big class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->pinjamanAktifCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Jumlah Pinjaman Overdue</h2>
            <div class="grid items-center grid-cols-2 pt-3">
                <div class="text-green-500">
                    <x-lucide-calendar-x-2 class="size-10"/>
                </div>
                <div class="flex justify-end">
                    <span class="text-2xl font-semibold">{{$this->pinjamanOverdueCount()}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Total Pinjaman Pending</h2>
            <div class="grid items-center grid-cols-5 pt-3">
                <div class="text-green-500">
                    <x-lucide-circle-dashed class="size-10"/>
                </div>
                <div class="flex justify-end col-span-4">
                    <span class="text-2xl font-semibold">Rp. {{$this->toRupiah($this->pinjamanPendingTotal())}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Total Pinjaman Aktif</h2>
            <div class="grid items-center grid-cols-5 pt-3">
                <div class="text-green-500">
                    <x-lucide-square-check-big class="size-10"/>
                </div>
                <div class="flex justify-end col-span-4">
                    <span class="text-2xl font-semibold">Rp. {{$this->toRupiah($this->pinjamanAktifTotal())}}</span>
                </div>
            </div>
        </div>
        <div class="px-5 py-5 bg-white rounded-lg shadow-lg shadow-slate-300/50">
            <h2 class="font-bold">Total Pinjaman Overdue</h2>
            <div class="grid items-center grid-cols-5 pt-3">
                <div class="text-green-500">
                    <x-lucide-calendar-x-2 class="size-10"/>
                </div>
                <div class="flex justify-end col-span-4">
                    <span class="text-2xl font-semibold">Rp. {{$this->toRupiah($this->pinjamanOverdueTotal())}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
