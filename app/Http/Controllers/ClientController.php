<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * عرض قائمة العملاء.
     */
    public function index(): View
    {
        $clients = Client::latest()->paginate(10);

        return view('clients.index', compact('clients'));
    }

    /**
     * عرض نموذج إضافة عميل.
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * حفظ عميل جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'تمت إضافة العميل بنجاح.');
    }

    /**
     * عرض بيانات عميل محدد.
     */
    public function show(Client $client): View
    {
        return view('clients.show', compact('client'));
    }

    /**
     * عرض نموذج تعديل بيانات العميل.
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * تحديث بيانات العميل.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }

    /**
     * حذف العميل.
     */
    public function destroy(Client $client)
    {
        if ($client->cases()->exists()) {
            return redirect()->route('clients.index')->with('error', 'لا يمكن حذف العميل لأنه مرتبط بقضية.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح.');
    }
}
