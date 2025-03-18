<div>
    {{-- Success is as dangerous as failure. --}}
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
                    <x-elements.button :href="route('user.list')" :variant="'success'" :style="'outlined'" :type="'link'">
                        <x-lucide-arrow-left class="size-5"/>
                        <span class="xs:block">Back to list page</span>
                    </x-button>
                </div>
                <div>

                    <x-elements.button :href="route('user.edit', $id)" :variant="'secondary'" :style="'outlined'" :type="'link'">
                        <x-lucide-square-pen class="size-5"/>
                        <span class="xs:block">Update</span>
                    </x-elements.button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <x-elements.header-form>User credentials</x-header-form>
            <x-elements.detail label="Username">{{ $this->checkVal($loadData['username'],'-') }}</x-elements.detail>
            <x-elements.detail label="Fullname">{{ $this->checkVal($loadData['name'],'-') }}</x-elements.detail>
            <x-elements.detail label="Email address">{{ $this->checkVal($loadData['email'],'-') }}</x-elements.detail>
            <x-elements.detail label="Mobile number">{{ $this->checkVal($loadData['mobile'],'-') }}</x-elements.detail>
            <x-elements.detail label="Profile photo">
                @php
                    $pp = ($loadData['profile_photo_path'] != 'avatar/blank-avatar.png') ? '/storage/'.$loadData['profile_photo_path'] : asset('assets/'.$loadData['profile_photo_path']);
                @endphp
                <img class='rounded-2xl' src="{{ $pp }}" width="130">
            </x-elements.detail>

            <x-elements.header-form>Validity</x-elements.header-form>
            <x-elements.detail label="Valid from">{{ $this->toDate($loadData['valid_from'],'d F Y') }}</x-elements.detail>
            <x-elements.detail label="Valid until">{{ $this->toDate($loadData['valid_until'],'d F Y','Forever') }}</x-elements.detail>

            <x-elements.header-form>Other information</x-elements.header-form>
            <x-elements.detail label="Remarks">{{ $this->checkVal($loadData['remarks'],'-') }}</x-elements.detail>
        </div>
    </div>
</div>
