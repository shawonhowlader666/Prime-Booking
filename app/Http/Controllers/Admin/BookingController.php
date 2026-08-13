<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['property', 'user', 'room'])->latest();

        // ── Fail-safe Status Filter (checks both booking_status & status columns)
        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            $query->where(function ($q) use ($status) {
                $q->where('booking_status', $status)
                  ->orWhere('status', $status);
            });
        }

        // ── Payment Status Filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $paymentStatus = strtolower($request->payment_status);
            $query->where('payment_status', $paymentStatus);
        }

        // ── Date Range Filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // ── Deep Omni-Search (Reference, Guest, Phone, Email, Property, User)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        $bookings = $query->paginate($perPage)->withQueryString();

        // ── Fail-Safe Real Time Financial & Booking KPI Statistics
        $stats = [
            'total'     => Booking::count(),
            'confirmed' => Booking::where(function ($q) {
                $q->where('booking_status', 'confirmed')->orWhere('status', 'confirmed');
            })->count(),
            'pending'   => Booking::where(function ($q) {
                $q->where('booking_status', 'pending')->orWhere('status', 'pending');
            })->count(),
            'cancelled' => Booking::where(function ($q) {
                $q->where('booking_status', 'cancelled')->orWhere('status', 'cancelled');
            })->count(),
            'revenue'   => (float) Booking::whereIn('payment_status', ['paid', 'completed'])
                ->sum(DB::raw('COALESCE(total_price, total_amount, 0)')),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show($id)
    {
        $booking = Booking::with(['property', 'user', 'room', 'addons'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,cancelled,completed,refunded',
        ]);

        $booking = Booking::findOrFail($id);
        
        // Synchronize both column aliases to prevent legacy view mismatches
        $booking->update([
            'booking_status' => $request->status,
            'status'         => $request->status,
        ]);

        return back()->with('success', 'Reservation status updated to ' . ucfirst($request->status) . ' successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|string|in:pending,paid,refunded,failed,unpaid',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Payment status updated to ' . ucfirst($request->payment_status) . ' successfully.');
    }

    public function exportCsv(Request $request)
    {
        $bookings = Booking::with(['property', 'user'])->latest()->get();

        $filename = "prime_booking_reservations_" . date('Y_m_d_H_i_s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Booking Reference', 'Guest Name', 'Phone', 'Email', 'Property Name', 'Check-In', 'Check-Out', 'Nights', 'Guests', 'Amount (BDT)', 'Booking Status', 'Payment Status', 'Date Booked']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_reference ?? 'PRM-'.$b->id,
                    $b->guest_name ?? optional($b->user)->name ?? 'Guest User',
                    $b->guest_phone ?? optional($b->user)->phone ?? 'N/A',
                    $b->guest_email ?? optional($b->user)->email ?? 'N/A',
                    $b->property?->name ?? $b->property_name ?? 'Hotel Stay',
                    $b->check_in,
                    $b->check_out,
                    $b->nights_count ?? 1,
                    $b->guests ?? 1,
                    $b->amount ?? 0,
                    ucfirst($b->effective_status),
                    ucfirst($b->payment_status ?? 'pending'),
                    $b->created_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $bookings = Booking::with(['property', 'user'])->latest()->get();
        return view('admin.bookings.pdf-report', compact('bookings'));
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking record deleted permanently.');
    }
}

