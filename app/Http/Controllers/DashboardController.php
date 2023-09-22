<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        if (auth()->check() && !auth()->user()->is_admin) {
            $userId = auth()->user()->id;
            $today  = Carbon::now()->format('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

            // Check if an attendance record exists for the user on the current day
            $existingAttendance = Attendance::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->first();

            if (!$existingAttendance) {
                // Create a new attendance record
                $time = Carbon::now()->format('H:i:s');
                if ($time >= '17:00:00') {
                    Attendance::create([
                        'user_id' => $userId,
                        'status'  => 'absen',
                    ]);
                }
                Attendance::create([
                    'user_id' => $userId,
                    'status'  => 'hadir',
                ]);
            }
        }

        $hadirCount = $this->hadir();
        $sakitCount = $this->sakit();
        $izinCount  = $this->izin();
        $absenCount = $this->absen();
        return view(
            'dashboard',
            [
                'hadirCount' => $hadirCount,
                'sakitCount' => $sakitCount,
                'izinCount'  => $izinCount,
                'absenCount' => $absenCount,
            ]
        );
    }
    public function hadir()
    {
        $today = Carbon::now()->format('Y-m-d');
        $hadir = Attendance::where('status', 'hadir')
            ->whereDate('created_at', $today)
            ->count();
        return $hadir;
    }

    public function sakit()
    {
        $today = Carbon::now()->format('Y-m-d');
        $sakit = Attendance::where('status', 'sakit')
            ->whereDate('created_at', $today)
            ->count();
        return $sakit;
    }

    public function izin()
    {
        $today = Carbon::now()->format('Y-m-d');
        $izin  = Attendance::where('status', 'izin')
            ->whereDate('created_at', $today)
            ->count();
        return $izin;
    }

    public function absen()
    {
        $today = Carbon::now()->format('Y-m-d');
        $absen = Attendance::where('status', 'absen')
            ->whereDate('created_at', $today)
            ->count();
        return $absen;
    }
}