<x-app-layout>
    <x-slot name="header">
        <div dir="rtl">
            <h1 class="text-xl font-bold" style="color: #302019;">
                إدارة العملاء
            </h1>
            <p class="mt-1 text-sm text-stone-500">
                عرض وتنظيم بيانات عملاء المكتب القانوني.
            </p>
        </div>
    </x-slot>

    <div dir="rtl" class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-7xl">

            {{-- رسائل النظام --}}
            @if (session('success'))
                <div role="status"
                    class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert"
                    class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">

                {{-- عنوان القائمة وزر الإضافة --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold" style="color: #302019;">
                            قائمة العملاء
                        </h2>
                        <p class="mt-1 text-sm text-stone-500">
                            العملاء المسجلون في النظام.
                        </p>
                    </div>

                    <a href="{{ route('clients.create') }}"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90"
                        style="background: #302019;">
                        + إضافة عميل
                    </a>
                </div>

                {{-- جدول العملاء --}}
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[680px] text-right text-sm">
                        <thead>
                            <tr class="border-y border-stone-200 bg-stone-50 text-stone-500">
                                <th scope="col" class="px-4 py-3 font-semibold">
                                    اسم العميل
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold">
                                    رقم واتساب
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold">
                                    تاريخ التسجيل
                                </th>
                                <th scope="col" class="px-4 py-3 font-semibold">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($clients as $client)
                                <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50/70">

                                    {{-- اسم العميل --}}
                                    <td class="px-4 py-4 font-semibold" style="color: #302019;">
                                        {{ $client->name }}
                                    </td>

                                    {{-- رقم واتساب --}}
                                    <td class="px-4 py-4 text-stone-600" dir="ltr">
                                        {{ $client->whatsapp_number }}
                                    </td>

                                    {{-- تاريخ التسجيل --}}
                                    <td class="px-4 py-4 whitespace-nowrap text-stone-500">
                                        {{ $client->created_at->format('Y/m/d') }}
                                    </td>

                                    {{-- الإجراءات --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">

                                            {{-- تعديل --}}
                                            <a href="{{ route('clients.edit', $client) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                                aria-label="تعديل بيانات {{ $client->name }}">
                                                تعديل
                                            </a>

                                            {{-- حذف --}}
                                            <form method="POST"
                                                action="{{ route('clients.destroy', $client) }}"
                                                onsubmit="return confirm('هل أنت متأكد من حذف العميل {{ $client->name }}؟ لا يمكن التراجع عن هذا الإجراء.');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    حذف
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center">
                                        <p class="font-semibold text-stone-600">
                                            لا يوجد عملاء حتى الآن
                                        </p>
                                        <p class="mt-2 text-xs text-stone-400">
                                            أضف أول عميل ليظهر هنا.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ترقيم الصفحات --}}
                @if ($clients->hasPages())
                    <div class="mt-5 border-t border-stone-100 pt-4">
                        {{ $clients->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>