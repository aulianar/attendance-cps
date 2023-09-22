<?php

namespace App\Http\Controllers;

use App\Jobs\ExportAttendance;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $paginate = 10;
    public function index()
    {
        $admin      = auth()->user()->is_admin;
        $search     = request('search');
        $searchDate = request('searchDate'); // Ambil nilai pencarian tanggal

        // Create a base query for the Attendance model
        $query = Attendance::query();

        if ($admin == false) {
            // Apply user_id filter for non-admin users
            $query->where('user_id', auth()->user()->id);
        }

        if ($search) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('status', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        } else {
            $query->where('user_id', '!=', '1');
        }

        // Apply the date filter, if provided
        if ($searchDate) {
            $query->whereDate('created_at', $searchDate);
        }

        // Apply order by clause after filtering
        $query->orderBy('created_at', 'desc');

        // Paginate the results
        $attendances = $query->paginate($this->paginate)->withQueryString();

        return view('attendance.index', compact('attendances', 'admin'));
    }


    public function exportPdf()
    {
        $admin      = auth()->user()->is_admin;
        $search     = request('search');
        $searchDate = request('searchDate'); // Ambil nilai pencarian tanggal

        // Create a base query for the Attendance model
        $query = Attendance::query();

        if ($admin == false) {
            // Apply user_id filter for non-admin users
            $query->where('user_id', auth()->user()->id);
        }

        if ($search) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('status', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        } else {
            $query->where('user_id', '!=', '1');
        }

        // Apply the date filter, if provided
        if ($searchDate) {
            $query->whereDate('created_at', $searchDate);
        }

        // Get the current page of matching records based on the pagination query parameters
        $attendances = $query->paginate(request('per_page'));

        // Load the PDF view and pass the data
        $pdf = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);

        // Download the PDF file
        return $pdf->download('attendance.pdf');
    }


    public function create()
    {
        return view('attendance.create');
    }

    public function store(Request $request, Attendance $attendance)
    {
        $user_id = User::where('email', $request->email)->first();
        $status  = $request->status;
        $today   = Carbon::now()->format('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

        $request->validate([
            'email'  => 'required|email',
            'status' => 'required|in:hadir,sakit,izin,absen',
            // Make sure 'status' is one of these values
        ]);

        $existingAttendance = Attendance::where('user_id', $user_id->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.index')->with('danger', 'Attendance has been recorded before!');
        }

        $attendance = Attendance::create([
            'user_id' => $user_id->id,
            'status'  => $status,
        ]);
        // dd($status);
        return redirect()->route('attendance.index')->with('success', 'Create Attendance Success!');
    }

    public function sakit(Attendance $attendance)
    {
        if (auth()->user()->is_admin == true) {
            $attendance->update([
                'status' => 'sakit'
            ]);
            return redirect()->route('attendance.index')->with('success', 'Status has been updated successfully!');
        } else {
            return redirect()->route('attendance.index')->with('danger', 'Failed when update status!');

        }
    }
    public function hadir(Attendance $attendance)
    {
        if (auth()->user()->is_admin == true) {
            $attendance->update([
                'status' => 'hadir'
            ]);
            return redirect()->route('attendance.index')->with('success', 'Status has been updated successfully!');
        } else {
            return redirect()->route('attendance.index')->with('danger', 'Failed when update status!');

        }
    }
    public function izin(Attendance $attendance)
    {
        if (auth()->user()->is_admin == true) {
            $attendance->update([
                'status' => 'izin'
            ]);
            return redirect()->route('attendance.index')->with('success', 'Status has been updated successfully!');
        } else {
            return redirect()->route('attendance.index')->with('danger', 'Failed when update status!');

        }
    }
    public function absen(Attendance $attendance)
    {
        if (auth()->user()->is_admin == true) {
            $attendance->update([
                'status' => 'absen'
            ]);
            return redirect()->route('attendance.index')->with('success', 'Status has been updated successfully!');
        } else {
            return redirect()->route('attendance.index')->with('danger', 'Failed when update status!');

        }
    }


    // $attendances = $response->getData()['attendances'];
    // dd($attendances);
    // $pdf = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    // return $pdf->download('attendance.pdf');


    // if (auth()->user()->is_admin) {
    //     $search     = request('search');
    //     $searchDate = request('searchDate');
    //     if ($search && $searchDate) {
    //         $attendances = Attendance::whereHas('user', function ($query) use ($search) {
    //             $query->where('status', 'like', '%' . $search . '%')
    //                 ->orWhere('name', 'like', '%' . $search . '%')
    //                 ->orWhere('email', 'like', '%' . $search . '%');
    //         })->whereDate('created_at', $searchDate)->get();
    //         $pdf         = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    //         return $pdf->download('attendance.pdf');
    //     } else if ($search) {
    //         $attendances = Attendance::whereHas('user', function ($query) use ($search) {
    //             $query->where('status', 'like', '%' . $search . '%')
    //                 ->orWhere('name', 'like', '%' . $search . '%')
    //                 ->orWhere('email', 'like', '%' . $search . '%');
    //         });
    //         $pdf         = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    //         return $pdf->download('attendance.pdf');
    //     } else if ($searchDate) {
    //         $attendances = Attendance::whereDate('created_at', $searchDate)->get();
    //         $pdf         = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    //         return $pdf->download('attendance.pdf');
    //     } else {
    //         $attendances = Attendance::all();
    //         $pdf         = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    //         return $pdf->download('attendance.pdf');
    //     }
    // } else {
    //     $attendances = Attendance::where('user_id', auth()->user()->id)->get();
    //     $pdf         = PDF::loadView('export.attendancepdf', ['attendances' => $attendances]);
    //     return $pdf->download('attendance.pdf');
    // }


}