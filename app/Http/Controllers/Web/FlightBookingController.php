<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\CurrencyService;

class FlightBookingController extends Controller
{
    private const AIRPORTS = [
        'DAC' => ['code' => 'DAC', 'city' => 'Dhaka', 'name' => 'Hazrat Shahjalal International Airport'],
        'CXB' => ['code' => 'CXB', 'city' => "Cox's Bazar", 'name' => "Cox's Bazar Airport"],
        'ZYL' => ['code' => 'ZYL', 'city' => 'Sylhet', 'name' => 'Osmani International Airport'],
        'CGP' => ['code' => 'CGP', 'city' => 'Chittagong', 'name' => 'Shah Amanat International Airport'],
        'SPD' => ['code' => 'SPD', 'city' => 'Saidpur', 'name' => 'Saidpur Airport'],
        'JSR' => ['code' => 'JSR', 'city' => 'Jessore', 'name' => 'Jessore Airport'],
    ];

    private const AIRLINES = [
        'BS' => ['code' => 'BS', 'name' => 'US-Bangla Airlines', 'logo' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=100', 'color' => '#0b2545'],
        'VQ' => ['code' => 'VQ', 'name' => 'NOVOAIR', 'logo' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=100', 'color' => '#2067e1'],
        '2A' => ['code' => '2A', 'name' => 'Air Astra', 'logo' => 'https://images.unsplash.com/photo-1569154941061-e231b4725ef1?w=100', 'color' => '#d93025'],
        'BG' => ['code' => 'BG', 'name' => 'Biman Bangladesh Airlines', 'logo' => 'https://images.unsplash.com/photo-1506015391300-4802dc74de2e?w=100', 'color' => '#16a34a'],
    ];

    public function index(Request $request)
    {
        $origin = strtoupper((string) $request->query('origin', 'DAC'));
        $destination = strtoupper((string) $request->query('destination', 'CXB'));
        $date = $request->query('date', now()->addDays(2)->format('Y-m-d'));
        $passengers = max(1, (int) $request->query('passengers', 1));
        $cabinClass = $request->query('cabin', 'economy');

        $originInfo = self::AIRPORTS[$origin] ?? self::AIRPORTS['DAC'];
        $destInfo = self::AIRPORTS[$destination] ?? self::AIRPORTS['CXB'];

        // Curated Flights
        $flights = [
            [
                'id' => 'FL-8801',
                'airline' => self::AIRLINES['BS'],
                'flight_number' => 'BS-141',
                'departure_time' => '07:30 AM',
                'arrival_time' => '08:35 AM',
                'duration' => '1h 05m',
                'price' => 4800,
                'baggage' => '20 kg Check-in + 7 kg Cabin',
                'seats_left' => null,
            ],
            [
                'id' => 'FL-8802',
                'airline' => self::AIRLINES['VQ'],
                'flight_number' => 'VQ-931',
                'departure_time' => '10:15 AM',
                'arrival_time' => '11:20 AM',
                'duration' => '1h 05m',
                'price' => 5200,
                'baggage' => '20 kg Check-in + 7 kg Cabin',
                'seats_left' => null,
            ],
            [
                'id' => 'FL-8803',
                'airline' => self::AIRLINES['2A'],
                'flight_number' => '2A-411',
                'departure_time' => '02:45 PM',
                'arrival_time' => '03:50 PM',
                'duration' => '1h 05m',
                'price' => 4600,
                'baggage' => '20 kg Check-in + 7 kg Cabin',
                'seats_left' => null,
            ],
            [
                'id' => 'FL-8804',
                'airline' => self::AIRLINES['BG'],
                'flight_number' => 'BG-433',
                'departure_time' => '06:10 PM',
                'arrival_time' => '07:15 PM',
                'duration' => '1h 05m',
                'price' => 5500,
                'baggage' => '25 kg Check-in + 7 kg Cabin',
                'seats_left' => null,
            ],
        ];

        return view('pages.flights', compact('originInfo', 'destInfo', 'date', 'passengers', 'cabinClass', 'flights'));
    }

    public function book(Request $request)
    {
        $validated = $request->validate([
            'flight_number'  => 'required|string|max:20',
            'airline_name'   => 'required|string|max:100',
            'origin'         => 'required|string|max:100',
            'destination'    => 'required|string|max:100',
            'departure_time' => 'required|string|max:30',
            'flight_date'    => 'required|date|after_or_equal:today',
            'passenger_name' => 'required|string|min:2|max:100',
            'passenger_phone'=> ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'passenger_email'=> 'required|email|max:150',
            'amount'         => 'nullable|numeric|min:0',
        ]);

        $reference = 'FLT-' . strtoupper(Str::random(8));

        // We are not an airline GDS: the request goes to the ticketing desk, who confirm
        // the real fare and seat with the airline and then issue the ticket.
        \App\Models\Inquiry::create(array_intersect_key([
            'user_id'      => auth()->id(),
            'name'         => trim($validated['passenger_name']),
            'phone'        => trim($validated['passenger_phone']),
            'email'        => strtolower(trim($validated['passenger_email'])),
            'subject'      => "Flight ticket request {$reference}",
            'service_type' => 'Flight Ticket Request',
            'destination'  => $validated['destination'],
            'travel_date'  => $validated['flight_date'],
            'passengers'   => 1,
            'message'      => "Flight request {$reference}: {$validated['airline_name']} {$validated['flight_number']}, {$validated['origin']} to {$validated['destination']} on {$validated['flight_date']} at {$validated['departure_time']}.",
            'status'       => 'pending',
        ], array_flip(\Illuminate\Support\Facades\Schema::getColumnListing('inquiries'))));

        session()->put("ticket_{$reference}", array_merge($validated, [
            'pnr'       => $reference,
            'seat'      => 'Assigned at ticketing',
            'status'    => 'REQUEST RECEIVED - NOT A TICKET',
            'issued_at' => now()->format('Y-m-d H:i:s'),
            'amount'    => (float) ($validated['amount'] ?? 0),
        ]));

        return redirect()->route('flights.voucher', $reference)
            ->with('success', 'Your flight request has been received. Our ticketing team will call you to confirm the fare and issue your ticket.');
    }

    public function voucher(string $pnr)
    {
        $ticket = session("ticket_{$pnr}");
        abort_if(!$ticket, 404);

        return view('pages.flight-voucher', compact('ticket'));
    }
}
