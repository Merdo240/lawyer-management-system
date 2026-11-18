<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use Illuminate\View\View;
use App\Models\LegalCase;


class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sessions = Session::with('legalCase')
            ->orderBy('scheduled_at')
            ->paginate(10);

        return view('sessions.index', compact('sessions'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cases = LegalCase::orderBy('title')->get();

        return view('sessions.create', compact('cases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    

public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'integer', 'exists:legal_cases,id'],
            'scheduled_at' => ['required', 'date'],
            'details' => ['nullable', 'string'],
        ]);

        Session::create([
            'case_id' => $validated['case_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'details' => $validated['details'] ?? null,
            'status' => 'scheduled',
        ]);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'تمت إضافة الجلسة بنجاح.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    
public function edit(Session $session): View
    {
        $cases = LegalCase::orderBy('title')->get();

        return view('sessions.edit', compact('session', 'cases'));
    }


    /**
     * Update the specified resource in storage.
     */
    
public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'integer', 'exists:legal_cases,id'],
            'scheduled_at' => ['required', 'date'],
            'details' => ['nullable', 'string'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
        ]);

        $session->update($validated);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'تم تحديث الجلسة بنجاح.');
    }


    /**
     * Remove the specified resource from storage.
     */
    
public function destroy(Session $session)
    {
        if ($session->notificationLogs()->exists()) {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'لا يمكن حذف هذه الجلسة لأنها مرتبطة بسجل إشعارات. يمكنك تعديل حالتها إلى ملغاة بدلًا من حذفها.');
        }

        $session->delete();

        return redirect()
            ->route('sessions.index')
            ->with('success', 'تم حذف الجلسة بنجاح.');
    }

}
