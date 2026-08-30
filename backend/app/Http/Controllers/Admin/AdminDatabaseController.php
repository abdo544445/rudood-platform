<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDatabaseController extends Controller
{
    /**
     * List of tables excluded from explorer or masked
     */
    protected array $excludedTables = [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens'
    ];

    /**
     * Display database explorer and selected table rows.
     */
    public function index(Request $request)
    {
        $driver = DB::getDriverName();

        // 1. Fetch all public tables based on active driver
        if ($driver === 'sqlite') {
            $allTablesRaw = DB::select("
                SELECT name as table_name 
                FROM sqlite_master 
                WHERE type='table' AND name NOT LIKE 'sqlite_%'
                ORDER BY name ASC
            ");
        } else {
            $allTablesRaw = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
                ORDER BY table_name ASC
            ");
        }

        $tablesList = [];
        foreach ($allTablesRaw as $t) {
            $tableName = $t->table_name;
            if (in_array($tableName, $this->excludedTables)) {
                continue;
            }

            try {
                $count = DB::table($tableName)->count();
            } catch (\Exception $e) {
                $count = 0;
            }

            $tablesList[$tableName] = [
                'name'  => $tableName,
                'count' => $count,
            ];
        }

        // 2. Determine selected table
        $selectedTable = $request->input('table', array_key_first($tablesList) ?? 'users');
        if (!isset($tablesList[$selectedTable])) {
            $selectedTable = array_key_first($tablesList) ?? 'users';
        }

        // 3. Fetch column schema metadata for selected table
        if ($driver === 'sqlite') {
            $columnsMetaRaw = DB::select("PRAGMA table_info('{$selectedTable}')");
            $columnsMeta = [];
            foreach ($columnsMetaRaw as $col) {
                $columnsMeta[] = (object) [
                    'column_name'    => $col->name,
                    'data_type'      => $col->type ?: 'text',
                    'is_nullable'    => $col->notnull ? 'NO' : 'YES',
                    'column_default' => $col->dflt_value,
                ];
            }
        } else {
            $columnsMeta = DB::select("
                SELECT column_name, data_type, is_nullable, column_default
                FROM information_schema.columns
                WHERE table_schema = 'public' AND table_name = ?
                ORDER BY ordinal_position ASC
            ", [$selectedTable]);
        }

        $columnNames = array_map(fn($col) => $col->column_name, $columnsMeta);

        // 4. Query Builder with Search, Sort, and Pagination
        $query = DB::table($selectedTable);

        $search = trim($request->input('search', ''));
        if (!empty($search)) {
            $query->where(function ($q) use ($columnNames, $search, $driver) {
                foreach ($columnNames as $index => $col) {
                    $likeOp = ($driver === 'pgsql') ? 'ILIKE' : 'LIKE';
                    if ($index === 0) {
                        $q->where(DB::raw("CAST({$col} AS TEXT)"), $likeOp, "%{$search}%");
                    } else {
                        $q->orWhere(DB::raw("CAST({$col} AS TEXT)"), $likeOp, "%{$search}%");
                    }
                }
            });
        }

        // Sorting
        $sortCol = $request->input('sort', in_array('id', $columnNames) ? 'id' : ($columnNames[0] ?? ''));
        $sortDir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (in_array($sortCol, $columnNames)) {
            $query->orderBy($sortCol, $sortDir);
        }

        $perPage = (int) $request->input('per_page', 25);
        if ($perPage < 5 || $perPage > 200) {
            $perPage = 25;
        }

        $rows = $query->paginate($perPage)->appends($request->query());

        // Calculate database overall statistics
        $totalTables = count($tablesList);
        $totalRecords = array_sum(array_column($tablesList, 'count'));

        try {
            if ($driver === 'pgsql') {
                $dbSizeResult = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
                $dbSize = $dbSizeResult[0]->size ?? 'N/A';
            } elseif ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                $dbSize = file_exists($dbPath) ? round(filesize($dbPath) / 1024 / 1024, 2) . ' MB' : 'N/A';
            } else {
                $dbSize = 'N/A';
            }
        } catch (\Exception $e) {
            $dbSize = 'N/A';
        }

        return view('admin.database.index', compact(
            'tablesList',
            'selectedTable',
            'columnsMeta',
            'columnNames',
            'rows',
            'search',
            'sortCol',
            'sortDir',
            'perPage',
            'totalTables',
            'totalRecords',
            'dbSize'
        ));
    }

    /**
     * Fetch single record JSON for modal inspection.
     */
    public function getRecord(Request $request, $table, $id)
    {
        if (in_array($table, $this->excludedTables)) {
            return response()->json(['success' => false, 'message' => 'جدول محمي'], 403);
        }

        $record = DB::table($table)->where('id', $id)->first();
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'السجل غير موجود'], 404);
        }

        // Hide password hashes from payload
        if (isset($record->password)) {
            $record->password = '•••••••••••••••• [مشفّر Bcrypt]';
        }

        return response()->json([
            'success' => true,
            'table'   => $table,
            'id'      => $id,
            'record'  => $record,
        ]);
    }

    /**
     * Export table rows to CSV stream.
     */
    public function exportCsv(Request $request, $table): StreamedResponse
    {
        if (in_array($table, $this->excludedTables)) {
            abort(403, 'غير مصرح بتصدير هذا الجدول');
        }

        $columns = Schema::getColumnListing($table);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"export_{$table}_" . date('Y-m-d_His') . ".csv\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($table, $columns) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel Arabic character compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $columns);

            DB::table($table)->orderBy('id')->chunk(500, function ($rows) use ($handle, $columns) {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($columns as $col) {
                        $val = $row->{$col} ?? '';
                        if (is_array($val) || is_object($val)) {
                            $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        }
                        if ($col === 'password') {
                            $val = '[ENCRYPTED_HASH]';
                        }
                        $line[] = $val;
                    }
                    fputcsv($handle, $line);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Execute safe, read-only SQL query with latency monitoring and row guards.
     */
    public function runQuery(Request $request)
    {
        $rawSql = trim($request->input('sql', ''));

        if (empty($rawSql)) {
            return response()->json(['success' => false, 'message' => 'يرجى إدخال استعلام SQL صالح']);
        }

        // Strict read-only safety guard
        $forbiddenKeywords = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'GRANT', 'REVOKE', 'CREATE', 'REPLACE', 'EXEC', 'MERGE'];
        foreach ($forbiddenKeywords as $badWord) {
            if (preg_match('/\b' . $badWord . '\b/i', $rawSql)) {
                return response()->json([
                    'success' => false,
                    'message' => "عملية محظورة: يُسمح فقط باستعلامات القراءة (SELECT / EXPLAIN / WITH) للحفاظ على سلامة البيانات."
                ], 403);
            }
        }

        $startTime = microtime(true);

        try {
            // Apply limit if not present to protect memory
            $cleanSql = rtrim($rawSql, ';');
            if (!preg_match('/\bLIMIT\b/i', $cleanSql)) {
                $cleanSql .= " LIMIT 200";
            }

            $results = DB::select($cleanSql);
            $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

            $columns = [];
            if (!empty($results)) {
                $columns = array_keys((array) $results[0]);
            }

            return response()->json([
                'success'       => true,
                'query'         => $cleanSql,
                'columns'       => $columns,
                'rows'          => $results,
                'count'         => count($results),
                'latency_ms'    => $latencyMs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ أثناء تنفيذ الاستعلام: ' . $e->getMessage()
            ], 400);
        }
    }
}
