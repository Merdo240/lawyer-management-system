<span class="text-xs text-stone-400">02</span>
                        </div>
                        <p class="mt-4 text-sm text-stone-500">إجمالي القضايا</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">—</p>
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
                        <p class="mt-4 text-sm text-stone-500">الجلسات القادمة</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">—</p>
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
                        <p class="mt-1 text-3xl font-bold tracking-tight" style="color: #302019;">—</p>
                        <p class="mt-1 text-xs text-stone-400">تحتاج إلى المراجعة</p>
                    </article>

                </div>
            </section>

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
                    <article
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
                    </article>

                    {{-- Cases --}}
                    <article
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
                    </article>

                    {{-- Sessions --}}
                    <article
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
                    </article>

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