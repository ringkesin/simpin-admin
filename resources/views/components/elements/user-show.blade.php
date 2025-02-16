<div>
    {{-- Success is as dangerous as failure. --}}
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
            <x-header-form>User credentials</x-header-form>
            <x-detail label="Username">{{ setIfNull($d['username'],'-') }}</x-detail>
            <x-detail label="Fullname">{{ setIfNull($d['name'],'-') }}</x-detail>
            <x-detail label="User Role">{{ setIfNull($d['userRole']['role']['name'],'-') }}</x-detail>
            <x-detail label="Email address">{{ setIfNull($d['email'],'-') }}</x-detail>
            <x-detail label="Mobile number">{{ setIfNull($d['mobile'],'-') }}</x-detail>
            <x-detail label="Profile photo">
                @php
                    $pp = setIfNull($d['profile_photo_path'],'avatar/blank-avatar.png');
                @endphp
                <img class='rounded-2xl' src="{{ asset($pp) }}" width="130">
            </x-detail>

            <x-header-form>Validity</x-header-form>
            <x-detail label="Valid from">{{ toDate($d['valid_from'],'d F Y') }}</x-detail>
            <x-detail label="Valid until">{{ toDate($d['valid_until'],'d F Y','Forever') }}</x-detail>

            <x-header-form>Other information</x-header-form>
            <x-detail label="Remarks">{{ setIfNull($d['remarks'],'-') }}</x-detail>
        </div>
    </div>
</div>
