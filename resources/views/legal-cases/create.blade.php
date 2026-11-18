
<x-app-layout>
    <div dir="rtl" class="min-h-screen bg-[#F8F5F0] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">

            {{-- رأس الصفحة --}}
            <div class="mb-8">
                <a
                    href="{{ route('legal-cases.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 transition hover:text-[#302019]"
                >
                    <span aria-hidden="true">→</span>
                    العودة إلى القضايا
                </a>

                <h1 class="mt-5 text-2xl font-bold text-[#302019]">
                    إضافة قضية جديدة
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    أدخل بيانات القضية وحدد العملاء المرتبطين بها.
                </p>
            </div>

            {{-- أخطاء التحقق --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">يرجى تصحيح الأخطاء التالية:</p>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- النموذج --}}
            <form
                action="{{ route('legal-cases.store') }}"
                method="POST"
                class="space-y-6 rounded-2xl border border-[#E8E0D5] bg-white p-5 shadow-sm sm:p-8"
            >
                @csrf

                {{-- عنوان القضية --}}
                <div>
                    <label for="title" class="mb-2 block text-sm font-semibold text-[#302019]">
                        عنوان القضية <span class="text-red-600">*</span>
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                        placeholder="مثال: قضية مطالبة مالية"
                        class="w-full rounded-lg border border-[#DED5C9] bg-white px-4 py-3 text-sm text-[#302019] outline-none transition placeholder:text-gray-400 focus:border-[#A88443] focus:ring-2 focus:ring-[#A88443]/20"
                    >

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- العملاء --}}
                <div>
                    <label for="clients" class="mb-2 block text-sm font-semibold text-[#302019]">
                        العملاء المرتبطون بالقضية
                    </label>

                    <p class="mb-3 text-xs leading-6 text-gray-500">
                        يمكنك اختيار أكثر من عميل، مثل الورثة في قضية ميراث.
                    </p>

                    @if ($clients->isEmpty())
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                            لا يوجد عملاء مسجلون حاليًا. يمكنك حفظ القضية دون عميل، أو
                            <a href="{{ route('clients.create') }}" class="font-bold underline">
                                إضافة عميل أولًا
                            </a>.
                        </div>
                    @else
                        <select
                            id="clients"
                            name="clients[]"
                            multiple
                            size="{{ min(max($clients->count(), 3), 6) }}"
                            class="w-full rounded-lg border border-[#DED5C9] bg-white px-3 py-3 text-sm text-[#302019] outline-none focus:border-[#A88443] focus:ring-2 focus:ring-[#A88443]/20"
                        >
                            @foreach ($clients as $client)
                                <option
                                    value="{{ $client->id }}"
                                    @selected(in_array($client->id, old('clients', [])))
                                >
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-gray-500">
                            للتحديد المتعدد على الكمبيوتر، اضغط باستمرار على Ctrl أثناء اختيار الأسماء.
                        </p>
                    @endif

                    @error('clients')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('clients.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- حالة القضية --}}
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-[#302019]">
                        حالة القضية <span class="text-red-600">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full rounded-lg border border-[#DED5C9] bg-white px-4 py-3 text-sm text-[#302019] outline-none transition focus:border-[#A88443] focus:ring-2 focus:ring-[#A88443]/20"
                    >
                        <option value="active" @selected(old('status', 'active') === 'active')>
                            نشطة
                        </option>

                        <option value="closed" @selected(old('status') === 'closed')>
                            مغلقة
                        </option>
                    </select>

                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الظهور العام --}}
                <div class="rounded-xl border border-[#E8E0D5] bg-[#FCFAF7] p-4">
                    <label for="is_public" class="flex cursor-pointer items-start gap-3">
                        <input
                            type="checkbox"
                            id="is_public"
                            name="is_public"
                            value="1"
                            @checked(old('is_public'))
                            class="mt-1 h-4 w-4 rounded border-[#C8BBAA] text-[#A88443] focus:ring-[#A88443]"
                        >

                        <span>
                            <span class="block text-sm font-semibold text-[#302019]">
                                السماح بظهور القضية في الموقع العام
                            </span>

                            <span class="mt-1 block text-xs leading-6 text-gray-500">
                                اترك الخيار غير محدد لإبقاء القضية خاصة. تفعيل هذا الخيار
                                لا يعني عرض أسماء العملاء أو تفاصيل القضية للعامة.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- الأزرار --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#EEE7DE] pt-6 sm:flex-row sm:justify-start">
                    <a
                        href="{{ route('legal-cases.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-[#DED5C9] px-5 py-3 text-sm font-semibold text-[#302019] transition hover:bg-[#F8F5F0]"
                    >
                        إلغاء
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-[#302019] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#493326] focus:outline-none focus:ring-2 focus:ring-[#A88443] focus:ring-offset-2"
                    >
                        حفظ القضية
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
