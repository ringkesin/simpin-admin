<header class="relative flex flex-wrap w-full py-3 text-sm shadow-md md:justify-start md:flex-nowrap dark:bg-neutral-800 bg-slate-100">
    <nav class="max-w-[98rem] w-full mx-auto px-4 md:flex md:items-center md:justify-between">
        <div class="flex items-center justify-between">
            <!-- Navigation Toggle -->
            <div class="p-2 lg:hidden">
                <button type="button" class="flex items-center justify-center text-sm rounded-full text-gray-10 gap-x-3 size-8 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:hover:text-neutral-200 dark:focus:text-neutral-200" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-sidebar-content-push" aria-label="Toggle navigation" data-hs-overlay="#hs-sidebar-content-push">
                    <svg class="sm:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/><path d="m8 9 3 3-3 3"/></svg>
                    <svg class="hidden sm:block shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/><path d="m10 15-3-3 3-3"/></svg>
                    <span class="sr-only">Navigation Toggle</span>
                </button>
            </div>
            <!-- End Navigation Toggle -->
            <!-- Breadcrumbs -->
                {{$breadcrumb}}
            <!-- End Breadcrumbs -->
            <div class="md:hidden">
                <button type="button" class="block font-medium text-gray-100 rounded-full hs-dark-mode-active:hidden hs-dark-mode hover:bg-gray-200 hover:text-black focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" data-hs-theme-click-value="dark">
                    <span class="inline-flex items-center justify-center group shrink-0 size-9">
                      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                      </svg>
                    </span>
                </button>
                <button type="button" class="hidden font-medium text-gray-800 rounded-full hs-dark-mode-active:block hs-dark-mode hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-200 dark:focus:bg-neutral-800 dark:hover:text-black" data-hs-theme-click-value="light">
                    <span class="inline-flex items-center justify-center group shrink-0 size-9">
                      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2"></path>
                        <path d="M12 20v2"></path>
                        <path d="m4.93 4.93 1.41 1.41"></path>
                        <path d="m17.66 17.66 1.41 1.41"></path>
                        <path d="M2 12h2"></path>
                        <path d="M20 12h2"></path>
                        <path d="m6.34 17.66-1.41 1.41"></path>
                        <path d="m19.07 4.93-1.41 1.41"></path>
                      </svg>
                    </span>
                </button>
            </div>
        </div>
        <div id="hs-navbar-example" class="hidden overflow-hidden transition-all duration-300 hs-collapse basis-full grow md:block" aria-labelledby="hs-navbar-example-collapse">
            <div class="flex flex-col items-center gap-5 mt-5 md:flex-row md:justify-end md:mt-0 md:ps-5">
                <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] ">
                    <button id="hs-navbar-example-dropdown" type="button" class="flex items-center w-full font-medium text-gray-600 hs-dropdown-toggle hover:text-gray-400 focus:outline-none focus:text-gray-400 dark:text-neutral-400 dark:hover:text-neutral-500 dark:focus:text-neutral-500" aria-haspopup="menu" aria-expanded="false" aria-label="Profile">
                        {{-- Dropdown --}}
                        <span class="inline-flex items-center justify-center text-xs font-semibold leading-none text-white rounded-full bg-rose-500 size-8">
                        {{
                            collect(explode(' ', 'Vicky Andreas'))->map(function ($name) {
                                return strtoupper(substr($name, 0, 1));
                            })
                            ->take(2) // Mengambil dua inisial pertama
                            ->implode('')
                        }}
                        </span>
                        <span class="px-2 text-white md:hidden">{{'Vicky Andreas'}}</span>
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] ease-in-out duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 md:w-48 z-10 bg-white md:shadow-md rounded-lg p-1 space-y-1 dark:bg-neutral-800 md:dark:border dark:border-neutral-700 dark:divide-neutral-700 before:absolute top-full md:border before:-top-5 before:start-0 before:w-full before:h-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="hs-navbar-example-dropdown">
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300" href="" wire:navigate @click.prevent="isLoading = true; ">
                            <x-lucide-user class="text-cyan-500 dark:text-white size-5 "/>Account Setting
                        </a>
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300" href="">
                            <x-lucide-log-out class="text-rose-500 dark:text-white size-5 "/>Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
