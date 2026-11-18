
<x-app-layout>
    <div dir="rtl" class="min-h-screen bg-[#F8F5F0] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            {{-- عنوان الصفحة --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#302019]">
                        إدارة الجلسات
                    </h1>

                    <p class="mt-2 text-sm text-gray-600">
                        متابعة مواعيد جلسات القضايا وتفاصيلها.
                    </p>
                </div>

                <a
                    href="{{ route('sessions.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-[#302019] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#493326]"
                >
                    + إضافة جلسة جديدة
                </a>
            </div>

            {{-- رسائل النظام --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- جدول الجلسات --}}
            <div class="overflow-hidden rounded-xl border border-[#E8E0D5] bg-white shadow-sm">

                @if ($sessions->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <h2 class="text-lg font-semibold text-[#302019]">
                            لا توجد جلسات مسجلة
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            ابدأ بإضافة أول جلسة لإحدى القضايا.
                        </p>

                        <a
                            href="{{ route('sessions.create') }}"
                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-[#A88443] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#91713A]"
                        >
                            إضافة أول جلسة
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[850px] text-right text-sm">

                            <thead class="bg-[#F8F5F0] text-[#302019]">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">القضية</th>
                                    <th class="px-6 py-4 font-semibold">موعد الجلسة</th>
                                    <th class="px-6 py-4 font-semibold">التفاصيل</th>
                                    <th class="px-6 py-4 font-semibold">الحالة</th>
                                    <th class="px-6 py-4 font-semibold">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#EEE7DE]">
                                @foreach ($sessions as $session)
                                    <tr class="transition hover:bg-[#FCFAF7]">

                                        {{-- القضية المرتبطة --}}
                                        <td class="px-6 py-4 font-medium text-[#302019]">
                                            {{ $session->legalCase?->title ?? 'قضية غير محددة' }}
                                        </td>

                                        {{-- الموعد --}}
                                        <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                            {{ $session->scheduled_at->format('Y-m-d') }}
                                        </td>

                                        {{-- التفاصيل --}}
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $session->details ?: 'لا توجد تفاصيل' }}
                                        </td>

                                        
{{-- الحالة الزمنية والحالة الفعلية --}}
<td class="px-6 py-4">
    @if ($session->status === 'completed')
        <span class="inline-block rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
            مكتملة
        </span>
    @elseif ($session->status === 'cancelled')
        <span class="inline-block rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
            ملغاة
        </span>
    @elseif ($session->scheduled_at->isFuture())
        <span class="inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
            قادمة
        </span>
    @elseif ($session->scheduled_at->isToday())
        <span class="inline-block rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
            موعدها اليوم
        </span>
    @else
        <span class="inline-block rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-700">
            مضى موعدها — تحتاج مراجعة
        </span>
    @endif
</td>


                                        {{-- الإجراءات --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <a
                                                    href="{{ route('sessions.edit', $session) }}"
                                                    class="inline-flex items-center justify-center rounded-lg border border-[#DED5C9] px-3 py-2 text-xs font-semibold text-[#302019] transition hover:border-[#A88443] hover:bg-[#F8F5F0]"
                                                >
                                                    تعديل
                                                </a>

                                                <form
                                                    action="{{ route('sessions.destroy', $session) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50"
                                                    >
                                                        حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    {{-- التنقل بين الصفحات --}}
                    <div class="border-t border-[#EEE7DE] px-6 py-4">
                        {{ $sessions->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
