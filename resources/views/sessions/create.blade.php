
<x-app-layout>
    <div dir="rtl" class="min-h-screen bg-[#F8F5F0] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">

            {{-- عنوان الصفحة --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-[#302019]">
                    إضافة جلسة جديدة
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    أدخل بيانات الجلسة واربطها بالقضية المناسبة.
                </p>
            </div>

            {{-- أخطاء التحقق --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">
                        يرجى تصحيح الأخطاء التالية:
                    </p>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-xl border border-[#E8E0D5] bg-white p-6 shadow-sm sm:p-8">

                @if ($cases->isEmpty())
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        لا توجد قضايا مسجلة. يجب إضافة قضية أولًا قبل تسجيل جلسة.

                        <a
                            href="{{ route('legal-cases.create') }}"
                            class="mt-3 inline-block font-semibold underline"
                        >
                            إضافة قضية جديدة
                        </a>
                    </div>
                @else
                    <form action="{{ route('sessions.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- القضية --}}
                        <div>
                            <label for="case_id" class="mb-2 block text-sm font-semibold text-[#302019]">
                                القضية
                            </label>

                            <select
                                id="case_id"
                                name="case_id"
                                required
                                class="w-full rounded-lg border border-[#DED5C9] bg-white px-4 py-3 text-sm text-[#302019] focus:border-[#A88443] focus:ring-[#A88443]"
                            >
                                <option value="">اختر القضية</option>

                                @foreach ($cases as $case)
                                    <option
                                        value="{{ $case->id }}"
                                        @selected(old('case_id') == $case->id)
                                    >
                                        {{ $case->title }}
                                    </option>
                                @endforeach
                            </select>

                            @error('case_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- موعد الجلسة --}}
                        <div>
                            <label for="scheduled_at" class="mb-2 block text-sm font-semibold text-[#302019]">
                                تاريخ الجلسة
                            </label>

                            <input
                                type="date"
                                id="scheduled_at"
                                name="scheduled_at"
                                value="{{ old('scheduled_at') }}"
                                required
                                class="w-full rounded-lg border border-[#DED5C9] bg-white px-4 py-3 text-sm text-[#302019] focus:border-[#A88443] focus:ring-[#A88443]"
                            >

                            @error('scheduled_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- تفاصيل الجلسة --}}
                        <div>
                            <label for="details" class="mb-2 block text-sm font-semibold text-[#302019]">
                                تفاصيل الجلسة
                                <span class="font-normal text-gray-500">(اختياري)</span>
                            </label>

                            <textarea
                                id="details"
                                name="details"
                                rows="4"
                                placeholder="اكتب تفاصيل الجلسة أو الملاحظات المتعلقة بها..."
                                class="w-full rounded-lg border border-[#DED5C9] bg-white px-4 py-3 text-sm text-[#302019] focus:border-[#A88443] focus:ring-[#A88443]"
                            >{{ old('details') }}</textarea>

                            @error('details')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        

                        {{-- الأزرار --}}
                        <div class="flex flex-col-reverse gap-3 border-t border-[#EEE7DE] pt-6 sm:flex-row">
                            <a
                                href="{{ route('sessions.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-[#DED5C9] px-5 py-3 text-sm font-semibold text-[#302019] transition hover:bg-[#F8F5F0]"
                            >
                                إلغاء
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-[#302019] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#493326]"
                            >
                                حفظ الجلسة
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
