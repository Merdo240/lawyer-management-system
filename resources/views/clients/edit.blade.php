<x-app-layout>
    <x-slot name="header">
        <div dir="rtl">
            <h1 class="text-xl font-bold" style="color: #302019;">
                تعديل بيانات العميل
            </h1>
            <p class="mt-1 text-sm text-stone-500">
                حدّث بيانات العميل للتواصل معه ومتابعة قضاياه.
            </p>
        </div>
    </x-slot>

    <div dir="rtl" class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-2xl">

            {{-- عنوان القسم --}}
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('clients.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition hover:text-stone-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        aria-hidden="true">
                        <path d="M19 12H5m7 7-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    العودة للعملاء
                </a>

                <span class="text-xs font-medium tracking-wide text-stone-400">
                    إدارة العملاء
                </span>
            </div>

            {{-- بطاقة النموذج --}}
            <div class="w-full overflow-hidden rounded-2xl border border-stone-200/80 bg-white shadow-sm">

                {{-- ترويسة البطاقة --}}
                <div class="border-b border-stone-200 bg-stone-50/80 px-5 py-5 sm:px-7">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-amber-200"
                            style="background: #F8F5F0;">
                            <svg class="h-5 w-5" style="color: #302019;" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M12 20h9" stroke-linecap="round" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="font-bold" style="color: #302019;">
                                تعديل بيانات العميل
                            </h2>
                            <p class="mt-1 text-sm text-stone-500">
                                عدّل المعلومات المطلوبة ثم احفظ التغييرات.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('clients.update', $client) }}" id="client-edit-form"
                    class="p-5 sm:p-7">

                    @csrf
                    @method('PUT')

                    {{-- الحقول --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        {{-- اسم العميل --}}
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-stone-700">
                                اسم العميل
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-stone-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.7" aria-hidden="true">
                                        <circle cx="12" cy="8" r="4" />
                                        <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                                    </svg>
                                </div>

                                <input id="name" name="name" type="text"
                                    value="{{ old('name', $client->name) }}" required autocomplete="name"
                                    placeholder="الاسم الكامل"
                                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                    @error('name') aria-describedby="name-error" @enderror
                                    class="w-full rounded-xl border bg-white py-3 pr-11 pl-4 text-sm text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-amber-700 focus:ring-4 focus:ring-amber-700/10 {{ $errors->has('name') ? 'border-red-400' : 'border-stone-300' }}">
                            </div>

                            @error('name')
                                <p id="name-error" class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- رقم واتساب --}}
                        <div>
                            <label for="phone_local" class="mb-2 block text-sm font-semibold text-stone-700">
                                رقم واتساب
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="flex w-full overflow-hidden rounded-xl border bg-white transition focus-within:border-amber-700 focus-within:ring-4 focus-within:ring-amber-700/10 {{ $errors->has('whatsapp_number') ? 'border-red-400' : 'border-stone-300' }}"
                                dir="ltr">

                                {{-- اختيار رمز الدولة --}}
                                <div class="relative shrink-0 border-r border-stone-200 bg-stone-50">
                                    <label for="country_code" class="sr-only">
                                        رمز الدولة
                                    </label>

                                    <select id="country_code"
                                        class="h-full w-[100px] cursor-pointer appearance-none bg-transparent py-3 pl-2 pr-6 text-sm font-medium text-stone-700 outline-none sm:w-[112px]">
                                        <option value="+967">🇾🇪 +967</option>
                                        <option value="+966">🇸🇦 +966</option>
                                    </select>

                                    <svg class="pointer-events-none absolute right-2 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-stone-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        aria-hidden="true">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>

                                {{-- الرقم المحلي --}}
                                <div class="relative min-w-0 flex-1">
                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.7" aria-hidden="true">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.11L8.1 9.89a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.84.58 2.8.7a2 2 0 0 1 1.72 2.05Z" />
                                        </svg>
                                    </div>

                                    <input id="phone_local" type="tel" inputmode="tel" autocomplete="tel-national"
                                        placeholder="7XXXXXXXX" required
                                        aria-invalid="{{ $errors->has('whatsapp_number') ? 'true' : 'false' }}"
                                        @error('whatsapp_number') aria-describedby="phone-error" @enderror
                                        class="w-full min-w-0 border-0 bg-transparent py-3 pl-12 pr-3 text-left text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:ring-0">
                                </div>
                            </div>

                            {{-- الرقم الدولي الذي سيُحفظ في قاعدة البيانات --}}
                            <input type="hidden" name="whatsapp_number" id="whatsapp_number"
                                value="{{ old('whatsapp_number', $client->whatsapp_number) }}">

                            @error('whatsapp_number')
                                <p id="phone-error" class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-2 text-xs leading-5 text-stone-400">
                                اختر الدولة وأدخل الرقم المحلي دون رمز الدولة.
                            </p>
                        </div>
                    </div>

                    {{-- تنبيه توضيحي --}}
                    <div class="mt-7 flex gap-3 rounded-xl border border-amber-200/70 bg-amber-50/60 p-4">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" style="color: #A88443;" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 11v5m0-8h.01" stroke-linecap="round" />
                        </svg>

                        <p class="text-xs leading-6 text-stone-600">
                            تأكد من صحة رقم واتساب ورمز الدولة؛ سيُستخدم الرقم لاحقًا
                            للتواصل مع العميل بشأن مواعيد الجلسات.
                        </p>
                    </div>

                    {{-- الأزرار --}}
                    <div class="mt-7 flex flex-wrap items-center gap-3 border-t border-stone-100 pt-5">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-amber-800/20"
                            style="background: #302019;">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            حفظ التعديلات
                        </button>

                        <a href="{{ route('clients.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-6 py-3 text-sm font-semibold text-stone-600 transition hover:bg-stone-50 focus:outline-none focus:ring-4 focus:ring-stone-200">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-stone-400">
                الحقول التي تحمل علامة
                <span class="text-red-500">*</span>
                مطلوبة.
            </p>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('client-edit-form');

            if (!form) return;

            const countryCode = form.querySelector('#country_code');
            const localPhone = form.querySelector('#phone_local');
            const fullPhone = form.querySelector('#whatsapp_number');

            const previousValue = fullPhone.value.trim();

            // استعادة الرقم المخزن واستخراج رمز الدولة.
            if (previousValue) {
                const matchedCode = ['+967', '+966'].find(code =>
                    previousValue.startsWith(code)
                );

                if (matchedCode) {
                    countryCode.value = matchedCode;
                    localPhone.value = previousValue.slice(matchedCode.length);
                } else {
                    // لا نفترض رمز دولة غير معروف.
                    localPhone.value = previousValue;
                }
            }

            form.addEventListener('submit', () => {
                const local = localPhone.value
                    .trim()
                    .replace(/[\s()-]/g, '');

                const normalized = local.replace(/^0+/, '');

                fullPhone.value = countryCode.value + normalized;
            });
        })();
    </script>
</x-app-layout>
