<div>
    {{-- Stop trying to control. --}}
    <div class="grid grid-cols-2 mb-4 xs:grid-cols-1">
        <div>
            <h1 class="mb-1 text-2xl font-bold md:text-3xl text-slate-800">
                {{$titlePage}}
            </h1>
        </div>
    </div>
    <div class="p-6 bg-white border rounded-sm shadow-lg border-slate-200">
        <!----------------Action Button------------------------>
        <div class='px-4 py-4 mb-6 border rounded bg-slate-50 border-slate-200'>
            <div class="flex justify-between gap-2">
                <div>
                    <x-elements.button wire:navigate :href="route('account.delete-request.list')" :variant="'primary'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.detail label="Nama User">{{ $this->setIfNull($loadData['user']['name'], '-') }}</x-elements.detail>
            <x-elements.detail label="Nomor Anggota">{{ $this->setIfNull($loadData['anggota']['nomor_anggota'], '-') }}</x-elements.detail>
            <x-elements.detail label="Tanggal Join">{{ $this->setIfNull($loadData['anggota']['tanggal_masuk'], '-') }}</x-elements.detail>
            <x-elements.detail label="Alasan Permintaan Hapus Akun">{{ $this->setIfNull($loadData['remarks'], '-') }}</x-elements.detail>
            <x-elements.detail label="Status">{{ $this->setIfNull(ucfirst($loadData['status']), '-') }}</x-elements.detail>
            @if($this->loadData['status'] == 'open')
            <div class="grid mb-3 md:grid-cols-3 sm:grid-flow-row">
                <div class="px-3 text-sm font-medium md:col-span-1 md:text-right sm:text-left text-slate-500">
                    <x-elements.button wire:loading.attr="disabled" wire:confirm="Apakah anda yakin request ini di setujui ?" wire:click='approveSubmit'>
                        <div wire:loading wire:target="approveSubmit">
                            {{-- <svg class="w-5 h-5 mr-3 animate-spin" viewBox="0 0 24 24"> --}}
                                {{-- <span class="sr-only">Loading Save Data</span> --}}
                            {{-- </svg> --}}
                            <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
                                <span class="sr-only">Processing.....</span>
                            </span>
                            <span class="xs:block">
                                Processing
                            </span>
                        </div>
                        <div class='flex gap-x-1' wire:loading.remove wire:target="approveSubmit">
                            <x-lucide-check class="size-5"/>
                            <span class="xs:block">
                                Approve
                            </span>
                        </div>
                    </x-elements.button>
                </div>
                <div class="px-3 text-sm font-medium md:col-span-2 text-slate-800 dark:text-slate-100">
                    <x-elements.button :variant="'warning'" wire:loading.attr="disabled" wire:confirm="Apakah anda yakin request ini di reject ?" wire:click='rejectSubmit'>
                        <div wire:loading wire:target="rejectSubmit">
                            {{-- <svg class="w-5 h-5 mr-3 animate-spin" viewBox="0 0 24 24"> --}}
                                {{-- <span class="sr-only">Loading Save Data</span> --}}
                            {{-- </svg> --}}
                            <span class="me-1 animate-spin inline-block size-3 border-[2px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
                                <span class="sr-only">Processing.....</span>
                            </span>
                            <span class="xs:block">
                                Processing
                            </span>
                        </div>
                        <div class='flex gap-x-1' wire:loading.remove wire:target="rejectSubmit">
                            <x-lucide-x class="size-5"/>
                            <span class="xs:block">
                                Reject
                            </span>
                        </div>
                    </x-elements.button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
