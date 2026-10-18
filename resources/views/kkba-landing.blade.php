<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Koperasi Karyawan Brantas Abipraya menghadirkan beragam layanan untuk anggota KKBA.">
    <meta name="theme-color" content="#15803d">

    <title>KKBA — Koperasi Karyawan Brantas Abipraya</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: "Inter", "Segoe UI", system-ui, sans-serif;
        }

        @layer base {
            body {
                @apply bg-[#f8fbf7] font-sans text-slate-800 antialiased;
            }

            a, button {
                @apply focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-green-500/30 focus-visible:ring-offset-2;
            }
        }

        @layer utilities {
            .section-shell {
                @apply mx-auto w-full max-w-7xl px-5 sm:px-8 lg:px-12;
            }

            .eyebrow {
                @apply inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-3.5 py-1.5 text-xs font-bold tracking-[0.16em] text-green-800 uppercase;
            }

            .soft-shadow {
                box-shadow: 0 28px 70px -35px rgb(22 101 52 / 0.35);
            }
        }
    </style>
</head>
<body class="overflow-x-hidden">
    <a href="#beranda" class="fixed top-3 left-3 z-[60] -translate-y-24 rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition-transform focus:translate-y-0">
        Lewati ke konten utama
    </a>

    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/70 bg-white/90 backdrop-blur-xl">
        <div class="section-shell flex h-20 items-center justify-between gap-5">
            <a href="#beranda" class="flex min-w-0 items-center gap-3 rounded-lg" aria-label="KKBA, kembali ke beranda">
                <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <img src="{{ asset('images/logo-kkba.png') }}" alt="Logo KKBA" class="size-10 object-contain">
                </span>
                <span class="min-w-0 leading-tight">
                    <span class="block text-lg font-extrabold tracking-tight text-slate-950">KKBA</span>
                    <span class="hidden truncate text-xs font-medium text-slate-500 sm:block">Koperasi Karyawan Brantas Abipraya</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                <a href="#beranda" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-green-50 hover:text-green-800">Beranda</a>
                <a href="#layanan" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-green-50 hover:text-green-800">Layanan</a>
                <a href="#tentang" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-green-50 hover:text-green-800">Tentang KKBA</a>
                <a href="#kontak" class="ml-2 rounded-full bg-green-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-green-800">Kontak</a>
                <a href="{{ route('login') }}" class="ml-1 rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-800 transition hover:border-green-400 hover:bg-green-50 hover:text-green-800">Login</a>
            </nav>

            <button id="menu-button" type="button" class="grid size-11 place-items-center rounded-xl border border-slate-200 bg-white text-slate-800 transition hover:border-green-300 hover:bg-green-50 lg:hidden" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <svg id="menu-open-icon" aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg id="menu-close-icon" aria-hidden="true" class="hidden size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <nav id="mobile-menu" class="hidden border-t border-slate-100 bg-white px-5 py-4 shadow-xl lg:hidden" aria-label="Navigasi seluler">
            <div class="mx-auto grid max-w-7xl gap-1">
                <a href="#beranda" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800">Beranda</a>
                <a href="#layanan" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800">Layanan</a>
                <a href="#tentang" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800">Tentang KKBA</a>
                <a href="#kontak" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-green-50 hover:text-green-800">Kontak</a>
                <a href="{{ route('login') }}" class="mt-2 rounded-xl bg-green-700 px-4 py-3 text-center text-sm font-bold text-white hover:bg-green-800">Login</a>
            </div>
        </nav>
    </header>

    <main>
        <section id="beranda" class="relative scroll-mt-20 overflow-hidden pt-20">
            <div class="absolute inset-x-0 top-0 -z-10 h-full bg-[radial-gradient(circle_at_85%_20%,rgba(34,197,94,0.18),transparent_28%),radial-gradient(circle_at_15%_90%,rgba(250,204,21,0.13),transparent_26%)]"></div>
            <div class="section-shell grid min-h-[calc(100svh-5rem)] items-center gap-12 py-16 lg:grid-cols-[1.08fr_0.92fr] lg:py-24">
                <div class="max-w-3xl">
                    <span class="eyebrow">
                        <span class="size-2 rounded-full bg-green-500"></span>
                        Layanan untuk anggota
                    </span>
                    <h1 class="mt-7 text-5xl font-black leading-[0.98] tracking-[-0.055em] text-slate-950 sm:text-6xl lg:text-7xl">
                        Lebih dekat dengan <span class="text-green-600">layanan KKBA.</span>
                    </h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl">
                        Koperasi Karyawan Brantas Abipraya menghadirkan beragam layanan untuk mendukung kebutuhan anggota dalam satu wadah yang mudah dikenali.
                    </p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="#layanan" class="inline-flex items-center justify-center gap-2 rounded-full bg-green-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-green-700/20 transition hover:-translate-y-0.5 hover:bg-green-800">
                            Jelajahi Layanan
                            <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6" /></svg>
                        </a>
                        <a href="#kontak" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-3.5 text-sm font-bold text-slate-800 transition hover:-translate-y-0.5 hover:border-green-400 hover:text-green-800">
                            Hubungi Kami
                        </a>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-lg lg:max-w-none" aria-hidden="true">
                    <div class="absolute -top-10 -right-8 size-32 rounded-full border-[22px] border-yellow-300/50"></div>
                    <div class="absolute -bottom-8 -left-6 size-28 rounded-full bg-blue-900/10 blur-sm"></div>
                    <div class="soft-shadow relative overflow-hidden rounded-[2.25rem] border border-white/90 bg-white/80 p-6 backdrop-blur sm:p-8">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                            <div>
                                <p class="text-xs font-bold tracking-[0.18em] text-green-700 uppercase">Koperasi karyawan</p>
                                <p class="mt-2 text-xl font-extrabold text-slate-950">Beragam kebutuhan,<br>satu KKBA.</p>
                            </div>
                            <div class="grid size-20 place-items-center rounded-[1.75rem] bg-green-50 ring-1 ring-green-100">
                                <img src="{{ asset('images/logo-kkba.png') }}" alt="" class="size-16 object-contain">
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-green-600 p-5 text-white">
                                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7.5 12 3l8 4.5M5.5 9.5v7M9.5 9.5v7M14.5 9.5v7M18.5 9.5v7M3 20h18" /></svg>
                                <p class="mt-7 text-sm font-bold">Simpan &amp; pinjam</p>
                            </div>
                            <div class="rounded-2xl bg-slate-950 p-5 text-white">
                                <svg class="size-7 text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 8h12l1 12H5L6 8Z" /><path d="M9 9V6a3 3 0 0 1 6 0v3" /></svg>
                                <p class="mt-7 text-sm font-bold">Belanja kebutuhan</p>
                            </div>
                            <div class="col-span-2 flex items-center justify-between rounded-2xl bg-green-50 p-5 text-green-950">
                                <p class="text-sm font-bold">Kendaraan, kedai kopi,<br>dan layanan lainnya</p>
                                <div class="flex -space-x-2">
                                    <span class="grid size-10 place-items-center rounded-full border-2 border-green-50 bg-white text-green-700">●</span>
                                    <span class="grid size-10 place-items-center rounded-full border-2 border-green-50 bg-yellow-300 text-yellow-800">●</span>
                                    <span class="grid size-10 place-items-center rounded-full border-2 border-green-50 bg-blue-900 text-blue-200">●</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="layanan" class="scroll-mt-20 bg-white py-20 sm:py-28">
            <div class="section-shell">
                <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
                    <div>
                        <span class="eyebrow">Layanan KKBA</span>
                        <h2 class="mt-5 text-4xl font-black tracking-[-0.04em] text-slate-950 sm:text-5xl">Yang Anda perlukan, lebih mudah ditemukan.</h2>
                    </div>
                    <p class="max-w-2xl text-base leading-7 text-slate-600 lg:justify-self-end lg:text-lg">
                        Kenali enam layanan KKBA untuk kebutuhan simpan pinjam, tabungan, mobilitas, pilihan kedai, dan belanja anggota.
                    </p>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-green-600 text-white">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7.5 12 3l8 4.5M5.5 9.5v7M9.5 9.5v7M14.5 9.5v7M18.5 9.5v7M3 20h18" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">01</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">SimPin</h3>
                        <p class="mt-1 text-sm font-bold text-green-700">Simpan Pinjam</p>
                        <p class="mt-4 leading-7 text-slate-600">Layanan simpan pinjam untuk kebutuhan anggota KKBA.</p>
                    </article>

                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-green-100 text-green-800">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 8h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a3 3 0 0 1 3-3h12v4" /><path d="M16 14h2" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">02</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">Tabungan Anggota</h3>
                        <p class="mt-4 leading-7 text-slate-600">Fasilitas menabung yang disediakan bagi anggota KKBA.</p>
                    </article>

                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-blue-100 text-blue-900">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 17-1-5 2-5h12l2 5-1 5H5Z" /><path d="M7 17v2M17 17v2M7.5 13h.01M16.5 13h.01M7 7l1-3M17 7l-1-3" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">03</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">Sewa Kendaraan</h3>
                        <p class="mt-4 leading-7 text-slate-600">Layanan penyewaan kendaraan dari KKBA. Detail unit dan pemesanan dapat ditanyakan melalui kontak resmi.</p>
                    </article>

                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-amber-100 text-amber-800">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 8h12v6a5 5 0 0 1-5 5h-2a5 5 0 0 1-5-5V8Z" /><path d="M17 10h1a3 3 0 0 1 0 6h-2M8 4v2M12 4v2" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">04</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">Epic Coffee</h3>
                        <p class="mt-4 leading-7 text-slate-600">Kedai kopi dan pilihan snack di salah satu lokasi layanan KKBA.</p>
                    </article>

                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-green-100 text-green-800">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h2l2 10h9l2-7H7M9 20h.01M17 20h.01" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">05</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">Inspire Mart</h3>
                        <p class="mt-4 leading-7 text-slate-600">Layanan toko yang menyediakan beragam kebutuhan belanja.</p>
                    </article>

                    <article class="group rounded-[1.75rem] border border-slate-200 bg-[#f8fbf7] p-6 transition hover:-translate-y-1 hover:border-green-300 hover:shadow-xl hover:shadow-green-900/5 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-blue-100 text-blue-900">
                                <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 8h12v6a5 5 0 0 1-5 5h-2a5 5 0 0 1-5-5V8Z" /><path d="M17 10h1a3 3 0 0 1 0 6h-2M8 4c0-1 1-1 1-2M12 4c0-1 1-1 1-2" /></svg>
                            </span>
                            <span class="text-xs font-black tracking-[0.18em] text-slate-300">06</span>
                        </div>
                        <h3 class="mt-7 text-xl font-extrabold text-slate-950">Inspire Coffee</h3>
                        <p class="mt-4 leading-7 text-slate-600">Kedai kopi dan pilihan snack di lokasi yang berbeda dari Epic Coffee.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="tentang" class="scroll-mt-20 py-20 sm:py-28">
            <div class="section-shell">
                <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 px-6 py-14 text-white sm:px-12 lg:px-16 lg:py-20">
                    <div class="absolute -top-24 -right-20 size-72 rounded-full bg-green-500/20 blur-2xl" aria-hidden="true"></div>
                    <div class="absolute -bottom-24 left-1/3 size-64 rounded-full bg-blue-700/20 blur-3xl" aria-hidden="true"></div>
                    <div class="relative grid gap-10 lg:grid-cols-[0.7fr_1.3fr] lg:items-center">
                        <div>
                            <span class="inline-flex rounded-full border border-green-400/30 bg-green-400/10 px-3.5 py-1.5 text-xs font-bold tracking-[0.16em] text-green-300 uppercase">Tentang KKBA</span>
                            <h2 class="mt-5 text-4xl font-black tracking-[-0.04em] sm:text-5xl">Tumbuh dalam satu kebersamaan.</h2>
                        </div>
                        <div class="border-white/10 lg:border-l lg:pl-12">
                            <p class="text-lg leading-8 text-slate-300 sm:text-xl sm:leading-9">
                                <strong class="font-bold text-white">Koperasi Karyawan Brantas Abipraya (KKBA)</strong> menyediakan beragam layanan bagi anggotanya, mulai dari simpan pinjam dan tabungan hingga penyewaan kendaraan, kedai kopi, dan kebutuhan belanja.
                            </p>
                            <a href="#kontak" class="mt-7 inline-flex items-center gap-2 rounded-lg text-sm font-bold text-green-300 transition hover:text-green-200">
                                Terhubung dengan KKBA
                                <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="kontak" class="scroll-mt-20 bg-green-600 py-20 text-white sm:py-28">
            <div class="section-shell grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:gap-20">
                <div>
                    <span class="inline-flex rounded-full border border-white/30 bg-white/10 px-3.5 py-1.5 text-xs font-bold tracking-[0.16em] text-white uppercase">Kontak resmi</span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-0.04em] sm:text-5xl">Mari terhubung dengan KKBA.</h2>
                    <p class="mt-5 max-w-lg text-lg leading-8 text-green-50">Hubungi kami melalui telepon atau email untuk menanyakan informasi lebih lanjut mengenai layanan KKBA.</p>
                </div>

                <address class="grid gap-3 not-italic sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur sm:col-span-2 sm:p-6">
                        <div class="flex gap-4">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-green-700">
                                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold tracking-wider text-green-100 uppercase">Alamat</p>
                                <p class="mt-2 font-semibold leading-7">Jl. DI Panjaitan Kav. 14, Cawang, Jakarta 13340</p>
                            </div>
                        </div>
                    </div>

                    <a href="tel:0218516290" class="rounded-2xl border border-white/20 bg-white/10 p-5 transition hover:bg-white/20 sm:p-6">
                        <div class="flex gap-4">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-green-700">
                                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3H4a1 1 0 0 0-1 1c0 9.4 7.6 17 17 17a1 1 0 0 0 1-1v-3l-4-2-2 2c-3.5-1.5-6.5-4.5-8-8l2-2-2-4Z" /></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-bold tracking-wider text-green-100 uppercase">Telepon</span>
                                <span class="mt-2 block font-semibold">021-8516290</span>
                            </span>
                        </div>
                    </a>

                    <div class="rounded-2xl border border-white/20 bg-white/10 p-5 sm:p-6">
                        <div class="flex gap-4">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-green-700">
                                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 4h10v5H7zM5 9h14a2 2 0 0 1 2 2v7h-4v2H7v-2H3v-7a2 2 0 0 1 2-2Z" /><path d="M7 15h10v5H7z" /></svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold tracking-wider text-green-100 uppercase">Faks</p>
                                <p class="mt-2 font-semibold">021-8516095</p>
                            </div>
                        </div>
                    </div>

                    <a href="mailto:kopkar_brantasabipraya@yahoo.co.id" class="rounded-2xl border border-white/20 bg-white/10 p-5 transition hover:bg-white/20 sm:col-span-2 sm:p-6">
                        <div class="flex gap-4">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white text-green-700">
                                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-bold tracking-wider text-green-100 uppercase">Email</span>
                                <span class="mt-2 block break-all font-semibold sm:break-normal">kopkar_brantasabipraya@yahoo.co.id</span>
                            </span>
                        </div>
                    </a>
                </address>
            </div>
        </section>
    </main>

    <footer class="bg-slate-950 py-8 text-slate-400">
        <div class="section-shell flex flex-col items-center justify-between gap-5 text-center sm:flex-row sm:text-left">
            <a href="#beranda" class="flex items-center gap-3 rounded-lg">
                <span class="grid size-10 place-items-center rounded-xl bg-white">
                    <img src="{{ asset('images/logo-kkba.png') }}" alt="" class="size-8 object-contain">
                </span>
                <span>
                    <span class="block text-sm font-bold text-white">KKBA</span>
                    <span class="block text-xs">Koperasi Karyawan Brantas Abipraya</span>
                </span>
            </a>
            <p class="text-sm">&copy; {{ date('Y') }} Koperasi Karyawan Brantas Abipraya.</p>
        </div>
    </footer>

    <script>
        const menuButton = document.getElementById('menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');

        const setMenuState = (isOpen) => {
            mobileMenu.classList.toggle('hidden', !isOpen);
            openIcon.classList.toggle('hidden', isOpen);
            closeIcon.classList.toggle('hidden', !isOpen);
            menuButton.setAttribute('aria-expanded', String(isOpen));
            menuButton.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
        };

        menuButton.addEventListener('click', () => {
            setMenuState(menuButton.getAttribute('aria-expanded') !== 'true');
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMenuState(false));
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) setMenuState(false);
        });
    </script>
</body>
</html>
