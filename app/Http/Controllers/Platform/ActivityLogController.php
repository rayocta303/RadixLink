<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = PlatformActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        $query->filterByUser($request->input('user_id'))
            ->filterByAction($request->input('action'))
            ->filterByEntityType($request->input('entity_type'))
            ->filterByDateRange($request->input('start_date'), $request->input('end_date'));

        $logs = $query->paginate(50);

        $users = User::where('user_type', 'platform')
            ->orderBy('name')
            ->get(['id', 'name']);

        $actionTypes = PlatformActivityLog::getActionTypes();
        $entityTypes = PlatformActivityLog::getEntityTypes();

        return view('platform.activity-logs.index', compact(
            'logs',
            'users',
            'actionTypes',
            'entityTypes'
        ));
    }
}
