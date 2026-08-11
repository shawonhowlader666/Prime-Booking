<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email')->orderByDesc('created_at');

        // Filter by action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by model type
        if ($model = $request->input('model_type')) {
            $query->where('model_type', $model);
        }

        // Search by description or user name
        if ($search = $request->input('search')) {
            $query->where(fn($q) =>
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
            );
        }

        $logs = $query->paginate(50);

        return view('admin.activity-log', compact('logs'));
    }

    public function destroy($id)
    {
        ActivityLog::findOrFail($id)->delete();
        return back()->with('success', 'Log entry removed.');
    }

    public function clear()
    {
        ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
        return back()->with('success', 'Logs older than 90 days cleared.');
    }
}
