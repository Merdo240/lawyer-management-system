<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Client;

class LegalCaseController extends Controller
{
    /**
     * عرض قائمة القضايا.
     */
    public function index(): View
    {
        $cases = LegalCase::with('clients')
            ->latest()
            ->paginate(10);

        return view('legal-cases.index', compact('cases'));
    }

    /**
     * عرض نموذج إنشاء قضية جديدة.
     */
    public function create(): View
    {
        $clients = Client::orderBy('name')->get();

        return view('legal-cases.create', compact('clients'));
    }

    /**
     * حفظ قضية جديدة.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'status' => ['required', 'in:active,closed'],
        'is_public' => ['nullable', 'boolean'],
        'clients' => ['nullable', 'array'],
        'clients.*' => ['integer', 'distinct', 'exists:clients,id'],
    ]);

    $legalCase = LegalCase::create([
        'title' => $validated['title'],
        'status' => $validated['status'],
        'is_public' => $request->boolean('is_public'),
    ]);

    $legalCase->clients()->sync($validated['clients'] ?? []);

    return redirect()
        ->route('legal-cases.index')
        ->with('success', 'تمت إضافة القضية بنجاح.');
    }

    /**
     * عرض تفاصيل قضية محددة.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * عرض نموذج تعديل قضية.
     */
    public function edit(LegalCase $legalCase): View
    {
        $clients = \App\Models\Client::orderBy('name')->get();

    $legalCase->load('clients');

    return view('legal-cases.edit', compact('legalCase', 'clients'));

    }

    /**
     * تحديث قضية.
     */
    
    public function update(Request $request, LegalCase $legalCase)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,closed'],
            'is_public' => ['nullable', 'boolean'],
            'clients' => ['nullable', 'array'],
            'clients.*' => ['integer', 'distinct', 'exists:clients,id'],
        ]);

        $legalCase->update([
            'title' => $validated['title'],
            'status' => $validated['status'],
            'is_public' => $request->boolean('is_public'),
        ]);

        $legalCase->clients()->sync($validated['clients'] ?? []);

        return redirect()
            ->route('legal-cases.index')
            ->with('success', 'تم تحديث القضية بنجاح.');
    }


    /**
     * حذف قضية.
     */
    public function destroy(LegalCase $legalCase)
    {
        // التأكد من أن القضية لا تحتوي على جلسات
        if ($legalCase->sessions()->exists()) {
            return redirect()
                ->route('legal-cases.index')
                ->with('error', 'لا يمكن حذف القضية لأنها مرتبطة بجلسات. احذف الجلسات أو عالجها أولاً.');
        }

        // حذف القضية إذا لم تكن مرتبطة بجلسات
        $legalCase->delete();

        return redirect()
            ->route('legal-cases.index')
            ->with('success', 'تم حذف القضية بنجاح.');
    }
}