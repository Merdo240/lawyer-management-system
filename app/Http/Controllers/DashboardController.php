<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LegalCase;
use App\Models\Session;
use App\Models\NotificationLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalClients = Client::count();

        $totalCases = LegalCase::count();

        $upcomingSessions = Session::query()->where('status', 'scheduled')->whereDate('scheduled_at', '>=', today())->count();

        $failedNotifications = NotificationLog::query()->where('status', 'failed')->count();

        $recentSessions = Session::query()->with('legalCase')->where('status', 'scheduled')->whereDate('scheduled_at', '>=', today())->orderBy('scheduled_at')->limit(5)->get();

        $sessionsNeedingReview = Session::query()->with('legalCase')->where('status', 'scheduled')->whereDate('scheduled_at', '<', today())->orderBy('scheduled_at')->limit(5)->get();

        return view('dashboard', compact('totalClients', 'totalCases', 'upcomingSessions', 'failedNotifications', 'recentSessions', 'sessionsNeedingReview'));
    }
}
