<nav x-data="{ open: false, notificationsOpen: false, profileOpen: false }"
     dir="rtl"
     class="sticky top-0 z-50 w-full max-w-full border-b border-stone-200/80 bg-white shadow-sm">

    <div class="mx-auto flex h-16 w-full max-w-7xl min-w-0 items-center justify-between gap-2 px-3 sm:gap-3 sm:px-6 lg:px-8">

        {{-- Right: Logo and page title --}}
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">

            {{-- Logo placeholder --}}
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-dashed border-stone-300 sm:h-10 sm:w-10"
                 title="مساحة مخصصة للشعار">
                <svg class="h-5 w-5 text-stone-300"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.4"
                     aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <path d="M8 12h8M12 8v8"/>
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold leading-5 sm:text-sm"
                   style="color: #302019;">
                    مكتب المحاماة
                </p>

                <p class="mt-0.5 truncate text-[10px] text-stone-500 sm:text-xs">
                    {{ request()->routeIs('dashboard') ? 'لوحة التحكم' : 'إدارة المكتب القانوني' }}
                </p>
            </div>
        </div>

        {{-- Center: Search (desktop only) --}}
        <div class="hidden min-w-0 flex-1 items-center justify-center px-3 lg:flex xl:px-5">
            <div class="flex w-full max-w-md min-w-0 items-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-400">
                <svg class="h-4 w-4 shrink-0"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <span class="truncate">البحث في المكتب...</span>

                <span class="mr-auto shrink-0 rounded border border-stone-200 bg-white px-1.5 py-0.5 text-[10px] text-stone-400">
                    قريبًا
                </span>
            </div>
        </div>

        {{-- Left: Notifications, date, profile and mobile menu --}}
        <div class="flex shrink-0 items-center gap-1 sm:gap-2">

            {{-- Notifications --}}
            <div class="relative shrink-0">
                <button type="button"
                        @click="notificationsOpen = !notificationsOpen; profileOpen = false"
                        @click.outside="notificationsOpen = false"
                        :aria-expanded="notificationsOpen"
                        aria-label="الإشعارات"
                        class="relative flex h-9 w-9 items-center justify-center rounded-lg text-stone-600 transition hover:bg-stone-100"
                        style="color: #665548;">

                    <svg class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                </button>

                <div x-cloak
                     x-show="notificationsOpen"
                     x-transition
                     class="absolute left-0 top-12 z-50 w-64 max-w-[calc(100vw-1.5rem)] rounded-xl border border-stone-200 bg-white p-4 shadow-lg">

                    <p class="text-sm font-bold" style="color: #302019;">
                        الإشعارات
                    </p>

                    <p class="mt-2 text-xs leading-6 text-stone-500">
                        ستظهر هنا تنبيهات الجلسات والإشعارات بعد تفعيل نظام التنبيهات.
                    </p>
                </div>
            </div>

            {{-- Date: tablet and desktop only --}}
            <div class="hidden shrink-0 items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-xs text-stone-600 md:flex">
                <svg class="h-4 w-4 shrink-0"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.7"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M16 3v4M8 3v4M3 11h18"/>
                </svg>

                <span>{{ now()->format('Y/m/d') }}</span>
            </div>

            <div class="hidden h-8 w-px shrink-0 bg-stone-200 sm:block"></div>

            {{-- Profile dropdown --}}
            <div class="relative shrink-0">
                <button type="button"
                        @click="profileOpen = !profileOpen; notificationsOpen = false"
                        @click.outside="profileOpen = false"
                        :aria-expanded="profileOpen"
                        aria-label="الملف الشخصي"
                        class="flex h-10 w-10 items-center justify-center rounded-xl p-0.5 transition hover:bg-stone-50 sm:h-auto sm:w-auto sm:gap-2 sm:p-1.5 sm:gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                         style="background: #F3EBDD; color: #92703A;">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>

                    <div class="hidden min-w-0 text-right sm:block">
                        <p class="max-w-32 truncate text-xs font-bold"
                           style="color: #302019;">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="mt-0.5 text-[11px] text-stone-500">
                            مدير المكتب
                        </p>
                    </div>

                    <svg class="hidden h-4 w-4 shrink-0 text-stone-400 sm:block"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         aria-hidden="true">
                        <path d="m7 10 5 5 5-5"/>
                    </svg>
                </button>

                <div x-cloak
                     x-show="profileOpen"
                     x-transition
                     class="absolute left-0 top-12 z-50 w-60 max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-xl border border-stone-200 bg-white shadow-lg">

                    <div class="border-b border-stone-100 px-4 py-3">
                        <p class="truncate text-sm font-bold"
                           style="color: #302019;">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="mt-1 break-all text-xs text-stone-500">
                            {{ Auth::user()->email }}
                        </p>

                        <p class="mt-2 text-xs" style="color: #92703A;">
                            مدير المكتب
                        </p>
                    </div>

                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm text-stone-600 transition hover:bg-stone-50 hover:text-stone-900">

                            <svg class="h-4 w-4 shrink-0"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.7"
                                 aria-hidden="true">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                            </svg>

                            الملف الشخصي
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50">

                                <svg class="h-4 w-4 shrink-0"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.7"
                                     stroke-linecap="round"
                                     stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="M10 17l5-5-5-5M15 12H3"/>
                                    <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                                </svg>

                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-label="فتح القائمة"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-stone-600 transition hover:bg-stone-100 lg:hidden">

                <svg x-show="!open"
                     class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <svg x-cloak
                     x-show="open"
                     class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     aria-hidden="true">
                    <path d="m6 6 12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile navigation --}}
    <div x-cloak
         x-show="open"
         x-transition
         class="w-full max-w-full border-t border-stone-200 bg-white px-4 py-3 lg:hidden">

        <a href="{{ route('dashboard') }}"
           class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold transition"
           style="{{ request()->routeIs('dashboard') ? 'background: #F8F5F0; color: #92703A;' : 'color: #665548;' }}">

            <svg class="h-5 w-5 shrink-0"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.7"
                 aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>

            لوحة التحكم
        </a>

        <a href="{{ route('clients.index') }}"
           class="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm text-stone-600 transition hover:bg-stone-50">

            <svg class="h-5 w-5 shrink-0"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.7"
                 aria-hidden="true">
                <circle cx="9" cy="8" r="4"/>
                <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                <path d="M19 8v6m3-3h-6"/>
            </svg>

            العملاء
        </a>

        <a href="{{ route('profile.edit') }}"
           class="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm text-stone-600 transition hover:bg-stone-50">
            الملف الشخصي
        </a>

        <div class="mt-3 border-t border-stone-100 pt-3 text-xs text-stone-500">
            {{ now()->format('Y/m/d') }}
        </div>
    </div>

    <div class="h-0.5 w-full max-w-full" style="background: #A88443;"></div>
</nav>