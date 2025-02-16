<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="grid grid-cols-2 mb-6 xs:grid-cols-1">
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
                    <x-elements.button :href="route('user.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to List Page</span>
                    </x-button>
                </div>
            </div>
        </div>
        <form wire:submit="saveUpdate">
            <!----------------User Credentials Section------------------------>
            <x-elements.header-form>User credentials</x-header-form>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Username -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="username">
                                Username <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="username" wire:model.lazy="username"/>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <!-- Group Password -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="password">
                                Password
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="password" wire:model.lazy="password"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-10">
                <!-- Kolom Kiri -->
                <div class="col-span-12 md:col-span-6">
                    <x-elements.header-form>User information</x-elements.header-form>
                    <!-- Group Fullname -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="name">
                                Fullname <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="name" wire:model.lazy="name"/>
                        </div>
                    </div>
                    <!-- Group Email -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="email">
                                Email <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="email" wire:model.lazy="email"/>
                        </div>
                    </div>
                    <!-- Group Mobile Number -->
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="mobile">
                                Mobile Number <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="text" name="mobile" wire:model.lazy="mobile"/>
                        </div>
                    </div>
                    <!-- Group Photo Profile -->
                    <div x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"  class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="profile_photo">
                                Photo Profile
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <div class="flex gap-5">
                                {{-- <div class='p-1 border rounded-2xl border-slate-200 dark:border-slate-600'> --}}
                                    @php
                                        $pp = ($profile_photo_path != 'avatar/blank-avatar.png') ? '/storage/'.$profile_photo_path : asset('assets/'.$profile_photo_path);
                                    @endphp
                                    <img class='rounded-xl ' src="{{ $pp }}" width="100">
                                {{-- </div> --}}
                                <div>
                                    <x-form.input type="file" name="profile_photo" wire:model.lazy="profile_photo" />
                                </div>
                                <div x-show="uploading" class="relative w-full h-4 mt-4 bg-gray-200 rounded-full shadow-inner md:mt-0 md:ml-4">
                                    <div class="absolute top-0 left-0 h-4 bg-blue-500 rounded-full"
                                        x-bind:style="`width: ${progress}%;`"></div>
                                </div>
                                <div x-show="uploading" class="px-2 text-sm text-center text-gray-500">
                                    Uploading... <span x-text="`${progress}%`"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-span-12 md:col-span-6">
                    <x-elements.header-form>User Validity</x-elements.header-form>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_from">
                                Valid Dari <span class="text-red-500">*</span>
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" id="valid_from" name="valid_from" :value="''" wire:model.lazy="valid_from"/>
                        </div>
                    </div>
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-4">
                            <x-form.label for="valid_until">
                                Valid Sampai
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-8">
                            <x-form.input class="w-full" type="date" id="valid_until" name="valid_until" :value="''" wire:model.lazy="valid_until"/>
                        </div>
                    </div>
                </div>
            </div>
            <x-elements.header-form>Other information</x-elements.header-form>
            <div class="grid grid-cols-12 gap-10">
                <div class="col-span-12">
                    <div class="grid items-center grid-cols-12 gap-4 mb-4">
                        <div class="col-span-12 md:col-span-2">
                            <x-form.label for="remarks">
                                Remarks
                            </x-form.label>
                        </div>
                        <div class="col-span-12 md:col-span-10">
                            <x-form.textarea class="w-full" id="remarks" name="remarks" wire:model.lazy="remarks"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-10">
                <div class="col-span-12">
                    <x-elements.button-submit class="mt-5" wire:loading.attr="disabled" wire:confirm="Are you sure your data is correct?">
                        <div wire:loading wire:target="saveUpdate">
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
                        <div class='flex gap-x-1' wire:loading.remove wire:target="saveUpdate">
                            <x-lucide-square-pen class="size-5"/>
                            <span class="xs:block">
                                Update data
                            </span>
                        </div>
                    </x-elements.button-submit>
                </div>
            </div>
        </form>
    </div>
</div>
