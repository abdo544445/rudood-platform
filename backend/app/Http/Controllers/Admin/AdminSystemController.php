<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class AdminSystemController extends Controller
{
    public function index()
    {
        // 1. Database Info
        $db_status = 'متصل (Connected)';
        $db_size_mb = 0;
        $tables_info = [];
        $driver = DB::getDriverName();
        $db_driver_name = match($driver) {
            'pgsql'  => 'PostgreSQL (Vector DB)',
            'mysql'  => 'MySQL 8.0 Primary',
            'sqlite' => 'SQLite 3 (Dev / Local)',
            default  => strtoupper($driver),
        };

        try {
            if ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (File::exists($dbPath)) {
                    $db_size_mb = round(File::size($dbPath) / 1024 / 1024, 2);
                }
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $table) {
                    $tableName = $table->name;
                    $rowCount = 0;
                    try {
                        $rowCount = DB::table($tableName)->count();
                    } catch (\Throwable $e) {}

                    $tables_info[] = [
                        'name'   => $tableName,
                        'rows'   => $rowCount,
                        'size'   => 'SQLite DB',
                        'engine' => 'SQLite',
                    ];
                }
            } elseif ($driver === 'pgsql') {
                $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
                foreach ($tables as $table) {
                    $tableName = $table->table_name;
                    $rowCount = 0;
                    try {
                        $rowCount = DB::table($tableName)->count();
                    } catch (\Throwable $e) {}

                    $tables_info[] = [
                        'name'   => $tableName,
                        'rows'   => $rowCount,
                        'size'   => 'PostgreSQL Table',
                        'engine' => 'PostgreSQL',
                    ];
                }
            } else {
                $tables = DB::select('SHOW TABLE STATUS');
                foreach ($tables as $table) {
                    $size = round(($table->Data_length + $table->Index_length) / 1024 / 1024, 2);
                    $db_size_mb += $size;
                    $tables_info[] = [
                        'name'   => $table->Name,
                        'rows'   => $table->Rows,
                        'size'   => $size . ' MB',
                        'engine' => $table->Engine,
                    ];
                }
            }
        } catch (\Exception $e) {
            $db_status = 'خطأ بالاتصال: ' . $e->getMessage();
        }

        // 2. Redis Info
        $redis_status = 'غير متصل (Disconnected)';
        $redis_keys_count = 0;
        $redis_memory = 'N/A';

        try {
            if (class_exists(\Illuminate\Support\Facades\Redis::class)) {
                $redis_info = \Illuminate\Support\Facades\Redis::info();
                $redis_status = 'متصل (Connected)';
                $used_mem = $redis_info['used_memory'] ?? ($redis_info['Memory']['used_memory'] ?? 0);
                $redis_memory = round((int)$used_mem / 1024 / 1024, 2) . ' MB';
                $redis_keys_count = \Illuminate\Support\Facades\Redis::dbSize();
            }
        } catch (\Throwable $e) {
            $redis_status = 'غير مفعل (مكتبي)';
        }

        // 3. Queue Workers Info
        $failed_jobs_count = 0;
        $pending_jobs_count = 0;

        try {
            $failed_jobs_count = DB::table('failed_jobs')->count();
            $pending_jobs_count = DB::table('jobs')->count();
        } catch (\Exception $e) {}

        // 4. WebSocket Server Info
        $websocket_status = 'معطل (Down)';
        $ws_url = config('services.websocket_url', 'http://localhost:3000');

        try {
            $res = Http::timeout(3)->get($ws_url . '/health');
            if ($res->successful()) {
                $websocket_status = 'يعمل بكفاءة (Running)';
            }
        } catch (\Exception $e) {
            try {
                $res = Http::timeout(2)->get('http://127.0.0.1:3000');
                $websocket_status = 'يعمل بكفاءة (Running - Local fallback)';
            } catch (\Exception $ex) {}
        }

        // 5. System Environment & Storage
        $php_version = PHP_VERSION;
        $laravel_version = app()->version();
        $environment = config('app.env');
        $debug_mode = config('app.debug') ? 'مفعل (Enabled)' : 'معطل (Disabled)';

        $storage_path = storage_path('app');
        $storage_size = 0;
        if (File::exists($storage_path)) {
            foreach (File::allFiles($storage_path) as $file) {
                $storage_size += $file->getSize();
            }
        }
        $storage_size_mb = round($storage_size / 1024 / 1024, 2);

        // 6. Recent Logs
        $recent_logs = [];
        $log_file = storage_path('logs/laravel.log');
        if (File::exists($log_file)) {
            $log_content = File::get($log_file);
            $lines = explode("\n", trim($log_content));
            $recent_logs = array_slice(array_reverse($lines), 0, 30);
        }

        // 7. System Maintenance Mode Configuration
        $maintenance = SystemSetting::getMaintenanceDetails();

        return view('admin.system.index', compact(
            'db_driver_name',
            'db_status',
            'db_size_mb',
            'tables_info',
            'redis_status',
            'redis_memory',
            'redis_keys_count',
            'failed_jobs_count',
            'pending_jobs_count',
            'websocket_status',
            'php_version',
            'laravel_version',
            'environment',
            'debug_mode',
            'storage_size_mb',
            'recent_logs',
            'maintenance'
        ));
    }

    /**
     * Display the public / client maintenance page.
     */
    public function showMaintenancePage()
    {
        $maintenance = SystemSetting::getMaintenanceDetails();
        return view('maintenance', compact('maintenance'));
    }

    /**
     * Enable or disable platform maintenance mode with scheduled countdown.
     */
    public function toggleMaintenance(Request $request)
    {
        $active = $request->boolean('is_active');
        $title = $request->input('title');
        $message = $request->input('message');
        $scheduledEndsAt = $request->input('scheduled_ends_at');

        $details = SystemSetting::setMaintenance($active, [
            'title'             => $title,
            'message'           => $message,
            'scheduled_ends_at' => $scheduledEndsAt,
            'activated_by'      => auth()->id(),
        ]);

        // Audit log
        try {
            AdminAuditLog::create([
                'user_id'     => auth()->id() ?? 1,
                'action'      => $active ? 'activate_maintenance' : 'deactivate_maintenance',
                'target_type' => 'system',
                'target_id'   => 0,
                'details'     => $details,
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $active ? 'تم تفعيل وضع الصيانة بنجاح.' : 'تم إلغاء تفعيل وضع الصيانة واستئناف المنصة.',
                'data'    => $details,
            ]);
        }

        return redirect()->back()->with(
            'success',
            $active ? 'تم تفعيل وضع الصيانة بنجاح وتوجيه الزوار للعد التنازلي المجدول.' : 'تم إيقاف وضع الصيانة واستعادة وصول المتاجر والعملاء بنجاح.'
        );
    }
}
