<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ActivityLog;
use App\Models\Tenant\TenantUser;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.activity-logs.index', [
                'logs' => collect(),
                'users' => collect(),
                'actionTypes' => [],
                'entityTypes' => [],
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        if (!Schema::connection('tenant')->hasTable('activity_logs')) {
            return view('tenant.activity-logs.index', [
                'logs' => collect(),
                'users' => collect(),
                'actionTypes' => ActivityLog::getActionTypes(),
                'entityTypes' => ActivityLog::getEntityTypes(),
                'dbError' => 'Tabel activity_logs belum tersedia. Silakan hubungi administrator untuk menjalankan migrasi database.',
            ]);
        }

        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        $query->filterByUser($request->input('user_id'))
            ->filterByAction($request->input('action'))
            ->filterByEntityType($request->input('entity_type'))
            ->filterByDateRange($request->input('start_date'), $request->input('end_date'));

        $logs = $query->paginate(50);

        $users = TenantUser::orderBy('name')
            ->get(['id', 'name']);

        $actionTypes = ActivityLog::getActionTypes();
        $entityTypes = ActivityLog::getEntityTypes();

        return view('tenant.activity-logs.index', compact(
            'logs',
            'users',
            'actionTypes',
            'entityTypes'
        ));
    }
}
