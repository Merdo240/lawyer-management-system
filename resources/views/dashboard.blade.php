<x-app-layout>
    <x-slot name="header">
        <div dir="rtl" class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold" style="color: #A88443;">
                    إدارة المكتب
                </p>
                <h1 class="mt-1 text-xl font-bold" style="color: #302019;">
                    لوحة التحكم
                </h1>
            </div>

            <div
                class="hidden items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-xs text-stone-500 sm:flex">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <rect x="3" y="4" width="18" height="17" rx="2" />
                    <path d="M16 2v4M8 2v4M3 10h18" />
                </svg>
                <span>{{ now()->format('Y/m/d') }}</span>
            </div>
        </div>
    </x-slot>

    <div dir="rtl" class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">

            {{-- Welcome --}}
            <section class="relative isolate overflow-hidden rounded-2xl" style="background: #302019;">
                <div class="pointer-events-none absolute -left-12 -top-16 h-48 w-48 rounded-full border"
                    style="border-color: rgba(168,132,67,.24);"></div>
                <div class="pointer-events-none absolute -left-2 -top-5 h-28 w-28 rounded-full border"
                    style="border-color: rgba(168,132,67,.2);"></div>
                <div class="pointer-events-none absolute bottom-0 right-0 h-1 w-24" style="background: #A88443;"></div>

                <div
                    class="relative z-10 flex flex-col gap-5 p-5 sm:p-7 lg:flex-row lg:items-center lg:justify-between lg:px-9 lg:py-7">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-xs font-medium" style="color: #D8C7A8;">
                            <span class="h-1.5 w-1.5 rounded-full" style="background: #A88443;"></span>
                            مساحة عملك
                        </div>

                        <h2 class="mt-2 text-2xl font-bold leading-relaxed text-white sm:text-3xl">
                            مرحبًا بعودتك،
                            <span style="color: #D6B675;">
                                {{ Auth::user()->name }}
                            </span>
                        </h2>

                        <p class="mt-2 max-w-2xl text-sm leading-7" style="color: #D8CBB9;">
                            تابع أعمال المكتب ونظّم العملاء والقضايا والجلسات من مكان واحد.
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-3 rounded-xl border p-3 sm:min-w-64 sm:p-4"
                        style="border-color: rgba(216,199,168,.2); background: rgba(255,255,255,.04);">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                            style="background: rgba(168,132,67,.16); color: #D6B675;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3v18M5 6h14M7 6l-4 8h8L7 6zm10 0-4 8h8l-4-8zM8 21h8" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                نظام إدارة المكتب
                            </p>
                            <p class="mt-1 text-xs" style="color: #D8CBB9;">
                                التنظيم والمتابعة اليومية
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Statistics --}}
            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-bold" style="color: #302019;">
                        ملخص المكتب
                    </h2>
                    <p class="mt-1 text-sm text-stone-500">
                        نظرة عامة على سجلات المكتب
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Clients --}}
                    <article
                        class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                                style="background: #F3EBDD; color: #92703A;">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="10" cy="7" r="4" />
                                    <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <span class="text-xs text-stone-400">01</span>
                        </div>
                        <p class="mt-4 text-sm text-stone-500">إجمالي العملاء</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">
                            {{ number_format($totalClients) }}
                        </p>
                        <p class="mt-1 text-xs text-stone-400">سجلات العملاء</p>
                    </article>

                    {{-- Cases --}}
                    <article
                        class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                                style="background: #F3EBDD; color: #92703A;">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6M8 13h8M8 17h5" />
                                </svg>
                            </div>
                            <span class="text-xs text-stone-400">02</span>
                        </div>
                        <p class="mt-4 text-sm text-stone-500">إجمالي القضايا</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">
                            {{ number_format($totalClients) }}
                        </p>
                        <p class="mt-1 text-xs text-stone-400">القضايا المسجلة</p>
                    </article>

                    {{-- Sessions --}}
                    <article
                        class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                                style="background: #F3EBDD; color: #92703A;">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2" />
                                    <path d="M16 3v4M8 3v4M3 11h18" />
                                </svg>
                            </div>
                            <span class="text-xs text-stone-400">03</span>
                        </div>
                        <p class="mt-4 text-sm text-stone-500">الجلسات المجدولة</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">
                            {{ number_format($upcomingSessions) }}</p>
                        <p class="mt-1 text-xs text-stone-400">الجلسات المجدولة</p>
                    </article>

                    {{-- Notifications --}}
                    <article
                        class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                                style="background: #F3EBDD; color: #92703A;">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                    <path d="M10 21h4" />
                                </svg>
                            </div>
                            <span class="text-xs text-stone-400">04</span>
                        </div>
                        <p class="mt-4 text-sm text-stone-500">الإشعارات الفاشلة</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">
                            {{ number_format($failedNotifications) }}</p>
                        <p class="mt-1 text-xs text-stone-400">تحتاج إلى المراجعة</p>
                    </article>

                </div>
            </section>

            {{-- Upcoming Sessions --}}
            <section>
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold" style="color: #302019;">
                            متابعة الجلسات
                        </h2>
                        <p class="mt-1 text-sm text-stone-500">
                            أقرب خمس جلسات مجدولة في المكتب
                        </p>
                    </div>

                    <a href="{{ route('sessions.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold" style="color: #92703A;">
                        جميع الجلسات
                        <span aria-hidden="true">←</span>
                    </a>
                </div>

                <div class="overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm">
                    @if ($recentSessions->isEmpty())
                        <div class="px-5 py-10 text-center">
                            <p class="font-semibold" style="color: #302019;">
                                لا توجد جلسات قادمة
                            </p>
                            <p class="mt-2 text-sm text-stone-500">
                                ستظهر هنا الجلسات المجدولة التي لم يحن موعدها بعد.
                            </p>
                            <a href="{{ route('sessions.create') }}"
                                class="mt-4 inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-white"
                                style="background: #302019;">
                                إضافة جلسة
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px] text-right text-sm">
                                <thead style="background: #F8F5F0; color: #6B625A;">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">القضية</th>
                                        <th class="px-5 py-3 font-semibold">موعد الجلسة</th>
                                        <th class="px-5 py-3 font-semibold">الحالة</th>
                                        <th class="px-5 py-3 font-semibold">الإجراء</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-stone-100">
                                    @foreach ($recentSessions as $session)
                                        <tr class="transition hover:bg-stone-50">
                                            <td class="px-5 py-4 font-semibold" style="color: #302019;">
                                                {{ $session->legalCase?->title ?? 'قضية غير محددة' }}
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-4 text-stone-600">
                                                {{ $session->scheduled_at->format('Y/m/d') }}
                                            </td>

                                            <td class="px-5 py-4">
                                                @if ($session->scheduled_at->isToday())
                                                    <span
                                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                                        style="background: #F3EBDD; color: #92703A;">
                                                        اليوم
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                                        style="background: #EEF4EA; color: #48653B;">
                                                        قادمة
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4">
                                                <a href="{{ route('sessions.edit', $session) }}"
                                                    class="font-semibold hover:underline" style="color: #92703A;">
                                                    عرض وتعديل
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Sessions Needing Review --}}
            @if ($sessionsNeedingReview->isNotEmpty())
                <section>
                    <div class="mb-4">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg"
                                style="background: #F8E8E5; color: #A33D32;">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 7v5l3 2" />
                                </svg>
                            </span>

                            <h2 class="text-lg font-bold" style="color: #302019;">
                                جلسات تحتاج إلى مراجعة
                            </h2>
                        </div>

                        <p class="mt-2 text-sm text-stone-500">
                            جلسات مضى موعدها ولم تُحدّث حالتها بعد.
                        </p>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px] text-right text-sm">
                                <thead style="background: #F8F5F0; color: #6B625A;">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">القضية</th>
                                        <th class="px-5 py-3 font-semibold">موعد الجلسة</th>
                                        <th class="px-5 py-3 font-semibold">التنبيه</th>
                                        <th class="px-5 py-3 font-semibold">الإجراء</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-stone-100">
                                    @foreach ($sessionsNeedingReview as $session)
                                        <tr class="transition hover:bg-stone-50">
                                            <td class="px-5 py-4 font-semibold" style="color: #302019;">
                                                {{ $session->legalCase?->title ?? 'قضية غير محددة' }}
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-4 text-stone-600">
                                                {{ $session->scheduled_at->format('Y/m/d') }}
                                            </td>

                                            <td class="px-5 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                                    style="background: #F8E8E5; color: #A33D32;">
                                                    مضى موعدها
                                                </span>
                                            </td>

                                            <td class="px-5 py-4">
                                                <a href="{{ route('sessions.edit', $session) }}"
                                                    class="font-semibold hover:underline" style="color: #92703A;">
                                                    مراجعة الجلسة
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-stone-100 px-5 py-3">
                            <a href="{{ route('sessions.index') }}" class="text-xs font-semibold hover:underline"
                                style="color: #92703A;">
                                عرض جميع الجلسات
                            </a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Quick links --}}
            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-bold" style="color: #302019;">
                        أقسام المكتب
                    </h2>
                    <p class="mt-1 text-sm text-stone-500">
                        وصول سريع إلى المهام الرئيسية
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    {{-- Clients --}}
                    <a href="{{ route('clients.index') }}"
                        class="flex items-start gap-4 rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm transition hover:border-stone-300 hover:shadow-md sm:p-6">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                            style="background: #F3EBDD; color: #92703A;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="10" cy="7" r="4" />
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold" style="color: #302019;">
                                إدارة العملاء
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-stone-500">
                                إضافة العملاء وتنظيم بيانات التواصل وربطهم بالقضايا.
                            </p>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-semibold"
                                style="color: #92703A;">
                                قسم العملاء
                                <span aria-hidden="true">←</span>
                            </span>
                        </div>
                    </a>

                    {{-- Cases --}}
                    <a href="{{ route('legal-cases.index') }}"
                        class="flex items-start gap-4 rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm transition hover:border-stone-300 hover:shadow-md sm:p-6">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                            style="background: #F3EBDD; color: #92703A;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <path d="M14 2v6h6M8 13h8M8 17h5" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold" style="color: #302019;">
                                إدارة القضايا
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-stone-500">
                                متابعة حالة القضايا وتفاصيلها والعملاء المرتبطين بها.
                            </p>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-semibold"
                                style="color: #92703A;">
                                قسم القضايا
                                <span aria-hidden="true">←</span>
                            </span>
                        </div>
                    </a>

                    {{-- Sessions --}}
                    <a href="{{ route('sessions.index') }}"
                        class="flex items-start gap-4 rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm transition hover:border-stone-300 hover:shadow-md sm:p-6">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                            style="background: #F3EBDD; color: #92703A;">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="16" rx="2" />
                                <path d="M16 3v4M8 3v4M3 11h18M8 15h2" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold" style="color: #302019;">
                                إدارة الجلسات
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-stone-500">
                                جدولة الجلسات ومتابعة المواعيد والتنبيهات.
                            </p>
                            <span class="mt-3 inline-flex items-center gap-2 text-xs font-semibold"
                                style="color: #92703A;">
                                قسم الجلسات
                                <span aria-hidden="true">←</span>
                            </span>
                        </div>
                    </a>

                </div>
            </section>

            {{-- Footer note --}}
            <div
                class="flex flex-col gap-1 border-t border-stone-200 pt-4 text-xs leading-6 text-stone-400 sm:flex-row sm:items-center sm:justify-between">
                <p>نظام إدارة المكتب القانوني</p>
                <p>الإحصائيات الفعلية ستظهر بعد ربطها ببيانات النظام.</p>
            </div>

        </div>
    </div>
</x-app-layout>
