<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminStatsService;

class AdminStatsController extends Controller
{
    public function __construct(private AdminStatsService $service) {}

    public function index()
    {
        $stats              = $this->service->globalStats();
        $dailyConversations = $this->service->dailyConversations(14);
        $dailyMessages      = $this->service->dailyMessages(14);
        $dailyNewWorkspaces = $this->service->dailyNewWorkspaces(14);
        $providerStats      = $this->service->aiProviderBreakdown();
        $aiDecisions        = $this->service->aiDecisionStats();
        $queueStats         = $this->service->queueStats();
        $workspaces         = $this->service->workspaceTable();
        $systemHealth       = $this->service->systemHealth();
        
        $subscriptionStats   = $this->service->subscriptionStats();
        $failedJobs          = $this->service->failedJobs();
        $dailyOperations     = $this->service->dailyOperations(14);
        $knowledgeHealth     = $this->service->knowledgeHealth();
        $channelConnectivity = $this->service->channelConnectivity();
        $providerUsage       = $this->service->providerUsage();

        return view('admin.statistics', compact(
            'stats',
            'dailyConversations',
            'dailyMessages',
            'dailyNewWorkspaces',
            'providerStats',
            'aiDecisions',
            'queueStats',
            'workspaces',
            'systemHealth',
            'subscriptionStats',
            'failedJobs',
            'dailyOperations',
            'knowledgeHealth',
            'channelConnectivity',
            'providerUsage'
        ));
    }

    public function live()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->recentLiveActivity(15)
        ]);
    }

    public function pruneFailed()
    {
        $count = $this->service->pruneFailedJobs();
        return redirect()->back()->with('success', "تم مسح $count مهمة متعثرة بنجاح.");
    }
}
