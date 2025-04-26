<div>
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
        <div class='px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200'>
            <div class="flex justify-between gap-2">
                <div>
                    <x-elements.button wire:navigate :href="route('main.tabungan.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex-none w-32">
                <x-form.label for="tahun">
                    Pilih Tahun <span class="text-red-500">*</span>
                </x-form.label>
            </div>
            <div class="flex-auto w-64 md:flex-initial">
                <x-form.select-single name="tahun" wire:model.lazy="tahun" class="w-full" id="tahun">
                    <option value="">Pilih Tahun</option>
                    @foreach($this->getYearRange(2) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </x-form.select-single>
            </div>
        </div>

        {{-- <div class='mt-6'>
            <div class="flex flex-col">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                        <div class="overflow-hidden border rounded-lg shadow-xs border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center text-slate-500" rowspan="2">Bulan</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-e-0 text-slate-500 border-slate-200" colspan="2">Simpanan Pokok</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-e-0 text-slate-500 border-slate-200" colspan="2">Simpanan Wajib</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-e-0 text-slate-500 border-slate-200" colspan="2">Tabungan Sukarela</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-e-0 text-slate-500 border-slate-200" colspan="2">Tabungan Indir</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-e-0 text-slate-500 border-slate-200" colspan="2">Kompensasi Masa Kerja</th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai s.d.</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai s.d.</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai s.d.</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai s.d.</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai</th>
                                        <th scope="col" class="px-3 py-2 text-sm font-normal text-center border border-t-0 border-b-0 text-slate-500 border-e-0 border-slate-200">Nilai s.d.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @foreach ($this->listMonth() as $key => $value)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-slate-500">{{ $value }}</th>
                                            <td class="px-3 py-2 text-sm">
                                                <input type="number" class="block w-full px-3 py-0.5 text-sm border-gray-200 rounded-lg focus:border-blue-600 focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none">
                                            </th>
                                            <td class="px-3 py-2 text-sm">Nilai s.d.</th>
                                            <td class="px-3 py-2 text-sm">Nilai</th>
                                            <td class="px-3 py-2 text-sm">Nilai s.d.</th>
                                            <td class="px-3 py-2 text-sm">Nilai</th>
                                            <td class="px-3 py-2 text-sm">Nilai s.d.</th>
                                            <td class="px-3 py-2 text-sm">Nilai</th>
                                            <td class="px-3 py-2 text-sm">Nilai s.d.</th>
                                            <td class="px-3 py-2 text-sm">Nilai</th>
                                            <td class="px-3 py-2 text-sm">Nilai s.d.</th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>
        </div> --}}
        
    </div>
</div>
