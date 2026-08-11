<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['property', 'user'])->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('booking_status', $request->status);
        }
        if ($request->payment && $request->payment !== 'all') {
            $query->where('payment_status', $request->payment);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_reference', 'like', '%' . $request->search . '%')
                  ->orWhere('guest_name', 'like', '%' . $request->search . '%')
                  ->orWhere('guest_phone', 'like', '%' . $request->search . '%')
                  ->orWhere('guest_email', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => Booking::count(),
            'confirmed' => Booking::where('booking_status', 'confirmed')->count(),
            'pending'   => Booking::where('booking_status', 'pending')->count(),
            'cancelled' => Booking::where('booking_status', 'cancelled')->count(),
            'revenue'   => Booking::where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show($id)
    {
        $booking = Booking::with(['property', 'user', 'room'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed,refunded']);
        $booking = Booking::findOrFail($id);
        $booking->update(['booking_status' => $request->status]);
        return back()->with('success', 'Booking status updated to ' . ucfirst($request->status) . ' successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate(['payment_status' => 'required|in:pending,paid,refunded,failed']);
        $booking = Booking::findOrFail($id);
        $booking->update(['payment_status' => $request->payment_status]);
        return back()->with('success', 'Payment status updated successfully.');
    }

    public function exportCsv(Request $request)
    {
        $bookings = Booking::with(['property', 'user'])->latest()->get();

        $filename = "prime_aviation_bookings_" . date('Y_m_d_H_i_s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Booking Reference', 'Guest Name', 'Phone', 'Email', 'Property', 'Check-In', 'Check-Out', 'Amount (BDT)', 'Booking Status', 'Payment Status', 'Date']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_reference,
                    $b->guest_name,
                    $b->guest_phone,
                    $b->guest_email,
                    $b->property?->name ?? 'Hotel Stay',
                    $b->check_in,
                    $b->check_out,
                    $b->total_amount,
                    $b->booking_status,
                    $b->payment_status,
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
        Booking::findOrFail($id)->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking record deleted successfully.');
    }
}
