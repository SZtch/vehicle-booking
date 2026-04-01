<?php

namespace App\Http\Controllers;

use App\Exports\BookingsExport;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'status'    => 'nullable|in:pending,approved_l1,approved,rejected',
        ]);

        ActivityLog::record(
            'exported_report',
            'Mengekspor laporan pemesanan kendaraan ke Excel',
            null,
            $request->only(['date_from', 'date_to', 'status'])
        );

        $filename = 'laporan-pemesanan-' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(
            new BookingsExport($request->date_from, $request->date_to, $request->status),
            $filename
        );
    }
}
