<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedirectLog;
use Carbon\Carbon;

class RedirectLogController extends Controller
{
    protected $redirectLogModel;

    public function __construct( RedirectLog $redirectLogModel)
    {
        $this->redirectLogModel = $redirectLogModel;
    }
    
    public function index($redirectId)
    {
        $logs = $this->redirectLogModel->where('redirect_id_code', $redirectId)->get();

        return response()->json($logs);
    }

    public function stats($redirectId)
    {
        $logs = RedirectLog::where('redirect_id_code', $redirectId)->get();

        $totalAccesses = $logs->count();

        $uniqueAccesses = $logs->unique('ip')->count();

        $topReferrers = $logs->groupBy('referer')->sortByDesc(function ($group) {
            return count($group);
        })->take(10)->map(function ($group) {
            return [
                'referer' => $group->first()->referer,
                'count' => count($group),
            ];
        });

        $lastTenDays = [];
        $startDate = Carbon::now()->subDays(9)->startOfDay(); 
        $endDate = Carbon::now()->endOfDay(); 

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $total = RedirectLog::where('redirect_id', $redirectId)
                ->whereDate('created_at', $date)
                ->count();

            $unique = RedirectLog::where('redirect_id', $redirectId)
                ->whereDate('created_at', $date)
                ->distinct('ip')
                ->count('ip');

            $lastTenDays[] = [
                'date' => $date->toDateString(),
                'total' => $total,
                'unique' => $unique,
            ];
        }
        return response()->json([
            'total_accesses' => $totalAccesses,
            'unique_accesses' => $uniqueAccesses,
            'top_referrers' => $topReferrers,
            'last_ten_days' => $lastTenDays,
        ]);
    }
}
