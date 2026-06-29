<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Call;
use App\Models\Correspondence;
use App\Models\FrontDesk;
use App\Models\Parcel;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->subDays(30)->startOfDay();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $totals = [
            'visitors' => Visitor::whereBetween('check_in_at', [$from, $to])->count(),
            'appointments' => Appointment::whereBetween('appointment_date', [$from, $to])->count(),
            'calls' => Call::whereBetween('call_time', [$from, $to])->count(),
            'correspondence' => Correspondence::whereBetween('created_at', [$from, $to])->count(),
            'parcels' => Parcel::whereBetween('created_at', [$from, $to])->count(),
            'front_desk' => FrontDesk::whereBetween('created_at', [$from, $to])->count(),
        ];

        $visitorsByDay = $this->countByDay(Visitor::class, 'check_in_at', $from, $to);
        $appointmentsByDay = $this->countByDay(Appointment::class, 'appointment_date', $from, $to);
        $callsByStatus = array_merge(
            ['answered' => 0, 'missed' => 0, 'voicemail' => 0, 'follow_up' => 0, 'callback_requested' => 0],
            Call::whereBetween('call_time', [$from, $to])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray()
        );
        $parcelsByStatus = array_merge(
            ['received' => 0, 'out_for_delivery' => 0, 'delivered' => 0, 'returned' => 0],
            Parcel::whereBetween('created_at', [$from, $to])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray()
        );
        $correspondenceByType = array_merge(
            ['incoming' => 0, 'outgoing' => 0, 'internal' => 0],
            Correspondence::whereBetween('created_at', [$from, $to])
                ->selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->pluck('total', 'type')
                ->toArray()
        );
        $frontDeskByStatus = array_merge(
            ['waiting' => 0, 'in_progress' => 0, 'completed' => 0],
            FrontDesk::whereBetween('created_at', [$from, $to])
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray()
        );

        return response()->json([
            'success' => true,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totals' => $totals,
            'visitorsByDay' => $visitorsByDay,
            'appointmentsByDay' => $appointmentsByDay,
            'callsByStatus' => $callsByStatus,
            'parcelsByStatus' => $parcelsByStatus,
            'correspondenceByType' => $correspondenceByType,
            'frontDeskByStatus' => $frontDeskByStatus,
        ]);
    }

    private function countByDay($model, $dateColumn, $from, $to)
    {
        $rows = $model::whereBetween($dateColumn, [$from, $to])
            ->selectRaw('DATE(' . $dateColumn . ') as date, count(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $data = [];
        $current = $from->copy();
        while ($current <= $to) {
            $label = $current->format('Y-m-d');
            $data[$label] = $rows[$label] ?? 0;
            $current->addDay();
        }
        return $data;
    }
}
