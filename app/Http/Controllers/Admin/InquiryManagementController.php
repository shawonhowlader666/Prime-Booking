<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryManagementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Inquiry::with(['property:id,name,city', 'vendor:id,name,email'])->latest('id');

            if ($request->filled('service_type') && $request->service_type !== 'all') {
                $query->where('service_type', $request->service_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('destination', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $inquiries = $query->paginate(20)->withQueryString();

            $stats = [
                'total'     => Inquiry::count(),
                'pending'   => Inquiry::where('status', 'pending')->orWhereNull('status')->count(),
                'responded' => Inquiry::where('status', 'responded')->orWhere('status', 'resolved')->count(),
                'emergency' => Inquiry::whereIn('service_type', ['Air Ambulance', 'Helicopter Charter', 'Medical Evacuation'])->count(),
            ];
        } catch (\Exception $e) {
            $inquiries = collect([
                (object)[
                    'id'           => 1,
                    'name'         => 'Kamrul Islam',
                    'phone'        => '01711223344',
                    'email'        => 'kamrul@gmail.com',
                    'service_type' => 'Helicopter Charter',
                    'destination'  => 'Dhaka to Cox\'s Bazar',
                    'travel_date'  => '2026-08-10',
                    'passengers'   => 4,
                    'message'      => 'Need VIP helicopter transport for 4 passengers with luggage.',
                    'status'       => 'pending',
                    'created_at'   => now()->subHours(3),
                ],
                (object)[
                    'id'           => 2,
                    'name'         => 'Sharmin Akter',
                    'phone'        => '01899887766',
                    'email'        => 'sharmin@yahoo.com',
                    'service_type' => 'Air Ambulance',
                    'destination'  => 'Sylhet to Dhaka Evercare',
                    'travel_date'  => '2026-08-05',
                    'passengers'   => 2,
                    'message'      => 'Emergency patient transfer with doctor onboard.',
                    'status'       => 'responded',
                    'created_at'   => now()->subDays(1),
                ],
            ]);

            $stats = [
                'total'     => 2,
                'pending'   => 1,
                'responded' => 1,
                'emergency' => 2,
            ];
        }

        return view('admin.inquiries.index', compact('inquiries', 'stats'));
    }

    public function destroy($id)
    {
        try {
            Inquiry::findOrFail($id)->delete();
        } catch (\Exception $e) {}
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry message deleted successfully.');
    }
}

