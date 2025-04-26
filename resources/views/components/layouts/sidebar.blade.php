@props([
    'menu_code' => null
])
<div>
    {{-- Stop trying to control. --}}
    <!-- Sidebar -->
    <div id="hs-sidebar-content-push" class="hs-overlay [--auto-close:lg] lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-64
    hs-overlay-open:translate-x-0
    -translate-x-full transition-all duration-300 transform
    h-full
    hidden
    fixed top-0 start-0 bottom-0 z-[60]
    bg-white shadow-lg shadow-slate-300" role="dialog" tabindex="-1" aria-label="Sidebar" >
        <div class="relative flex flex-col h-full max-h-full ">
            <!-- Header -->
            <header class="flex items-center justify-between p-4 gap-x-2">
                {{-- <a class="flex-none text-xl font-semibold text-black focus:outline-none focus:opacity-80" href="#" aria-label="Brand">Brand</a> --}}
                <img class="flex-none w-40" src="{{asset('/assets/media/logo/logokkba.svg')}}">

                <div class="lg:hidden -me-2">
                <!-- Close Button -->
                <button type="button" class="flex items-center justify-center text-sm bg-white border rounded-full text-slate-600 border-slate-200 gap-x-3 size-6 hover:bg-slate-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-slate-100" data-hs-overlay="#hs-sidebar-content-push">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    <span class="sr-only">Close</span>
                </button>
                <!-- End Close Button -->
                </div>
            </header>
            <!-- End Header -->
            <!-- Body -->

            <nav class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar-thumb]:bg-slate-300">
                <div class="flex flex-col flex-wrap w-full px-5 pb-0 hs-accordion-group" data-hs-accordion-always-open>
                    <ul class="space-y-1">
                        <li class='py-2'>
                            <h3 class="pl-3 text-xs font-semibold uppercase text-slate-400">
                                {{-- <span class="hidden w-6 text-center lg:block lg:sidebar-expanded:hidden 2xl:hidden" aria-hidden="true">•••</span> --}}
                                <span class="lg:sidebar-expanded:block 2xl:block">Menu</span>
                            </h3>
                        </li>
                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'dashboard' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('dashboard')}}">
                                <x-lucide-home class="size-4"/>
                                Dashboard
                            </a>
                        </li>

                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'tabungan' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('main.tabungan.list')}}">
                                <x-lucide-landmark class="size-4"/>
                                Tabungan
                            </a>
                        </li>

                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'tagihan' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('main.tagihan.list')}}">
                                <x-lucide-credit-card class="size-4"/>
                                Tagihan
                            </a>
                        </li>

                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'pinjaman' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('main.pinjaman.list')}}">
                                <x-lucide-banknote class="size-4"/>
                                Pinjaman
                            </a>
                        </li>

                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'shu' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('main.shu.list')}}">
                                <x-lucide-badge-percent class="size-4"/>
                                SHU
                            </a>
                        </li>

                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'konten' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('main.konten.list')}}">
                                <x-lucide-newspaper class="size-4"/>
                                Konten
                            </a>
                        </li>

                        <li class="hs-accordion" id="master-accordion">
                            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="master-accordion-collapse-1">
                                <x-lucide-database class="size-4"/>
                                    Master
                                <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div id="master-accordion-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ (in_array($menu_code, ['master-anggota','master-simulasi'])) ? '' : 'hidden'}}" role="region" aria-labelledby="master-accordion">
                                <ul class="pt-1 space-y-1 ps-7">
                                    <li>
                                        <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 text-sm {{$menu_code == 'master-anggota' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} rounded-lg" href="{{route('master.anggota.list')}}">
                                        Anggota
                                        </a>
                                    </li>
                                    <li>
                                        <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 text-sm {{$menu_code == 'master-simulasi' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} rounded-lg" href="{{route('master.simulasi.list')}}">
                                        Simulasi Pinjaman
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class='py-2'>
                            <h3 class="pl-3 text-xs font-semibold uppercase text-slate-400">
                                {{-- <span class="hidden w-6 text-center lg:block lg:sidebar-expanded:hidden 2xl:hidden" aria-hidden="true">•••</span> --}}
                                <span class="lg:sidebar-expanded:block 2xl:block">Configuration</span>
                            </h3>
                        </li>
                        <li>
                            <a wire:navigate class="flex items-center gap-x-3 py-2 px-2.5 {{$menu_code == 'user' ? 'bg-green-500 text-white' : 'text-slate-700 hover:bg-slate-100'}} text-sm rounded-lg" href="{{route('user.list')}}">
                                <x-lucide-user class="size-4"/>
                                User
                            </a>
                        </li>

                        {{-- <li class="hs-accordion" id="users-accordion">
                            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="users-accordion-collapse-1">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Users

                                <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div id="users-accordion-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="users-accordion">
                                <ul class="pt-1 space-y-1 hs-accordion-group ps-7" data-hs-accordion-always-open>
                                    <li class="hs-accordion" id="users-accordion-sub-1">
                                        <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="users-accordion-sub-1-collapse-1">
                                            Sub Menu 1

                                            <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                            <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                        </button>

                                        <div id="users-accordion-sub-1-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="users-accordion-sub-1">
                                            <ul class="pt-1 space-y-1 ps-2">
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 1
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 2
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 3
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="hs-accordion" id="users-accordion-sub-2">
                                        <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="users-accordion-sub-2-collapse-1">
                                            Sub Menu 2

                                            <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                            <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                        </button>

                                        <div id="users-accordion-sub-2-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="users-accordion-sub-2">
                                            <ul class="pt-1 space-y-1 ps-2">
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 1
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 2
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                                        Link 3
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="hs-accordion" id="account-accordion">
                            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="account-accordion-sub-1-collapse-1">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="15" r="3"/><circle cx="9" cy="7" r="4"/><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="m21.7 16.4-.9-.3"/><path d="m15.2 13.9-.9-.3"/><path d="m16.6 18.7.3-.9"/><path d="m19.1 12.2.3-.9"/><path d="m19.6 18.7-.4-1"/><path d="m16.8 12.3-.4-1"/><path d="m14.3 16.6 1-.4"/><path d="m20.7 13.8 1-.4"/></svg>
                                Account

                                <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <div id="account-accordion-sub-1-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="account-accordion">
                                <ul class="pt-1 space-y-1 ps-7">
                                    <li>
                                        <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                        Link 1
                                        </a>
                                    </li>
                                    <li>
                                        <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                        Link 2
                                        </a>
                                    </li>
                                    <li>
                                        <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                        Link 3
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="hs-accordion" id="projects-accordion">
                            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" aria-expanded="true" aria-controls="projects-accordion-sub-1-collapse-1">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.5 2H8.6c-.4 0-.8.2-1.1.5-.3.3-.5.7-.5 1.1v12.8c0 .4.2.8.5 1.1.3.3.7.5 1.1.5h9.8c.4 0 .8-.2 1.1-.5.3-.3.5-.7.5-1.1V6.5L15.5 2z"/><path d="M3 7.6v12.8c0 .4.2.8.5 1.1.3.3.7.5 1.1.5h9.8"/><path d="M15 2v5h5"/></svg>
                                Projects

                                <svg class="hidden text-slate-600 hs-accordion-active:block ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>

                                <svg class="block text-slate-600 hs-accordion-active:hidden ms-auto size-4 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <div id="projects-accordion-sub-1-collapse-1" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden" role="region" aria-labelledby="projects-accordion">
                                <ul class="pt-1 space-y-1 ps-7">
                                <li>
                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                    Link 1
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                    Link 2
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100 focus:outline-none focus:bg-slate-100" href="#">
                                    Link 3
                                    </a>
                                </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100" href="#">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                Calendar <span class="ms-auto py-0.5 px-1.5 inline-flex items-center gap-x-1.5 text-xs bg-slate-200 text-slate-800 rounded-full">New</span>
                            </a>
                        </li> --}}
                        <li>
                            <a class="flex items-center gap-x-3 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-slate-100" href="#">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                Documentation
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Body -->
            </div>
        </div>
        <!-- End Sidebar -->
    </div>

</div>
