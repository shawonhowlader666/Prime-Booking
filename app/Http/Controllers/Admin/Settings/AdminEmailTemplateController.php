<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminEmailTemplateController extends Controller
{
    /**
     * Seed default templates if table is empty.
     */
    protected function ensureDefaultsExist(): void
    {
        $count = EmailTemplate::count();
        if ($count > 0) {
            return;
        }

        $defaults = [
            [
                'code' => 'booking_confirmed',
                'name' => 'Booking Confirmation (Guest)',
                'category' => 'booking',
                'subject' => 'Booking Confirmed - #{booking_ref} | Prime Booking',
                'body_html' => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
    <div style="background:#1890ff;color:#ffffff;padding:24px;text-align:center;">
        <h1 style="margin:0;font-size:22px;">Booking Confirmation</h1>
        <p style="margin:6px 0 0;font-size:14px;opacity:0.9;">Ref: #{booking_ref}</p>
    </div>
    <div style="padding:24px;">
        <p>Dear <strong>{guest_name}</strong>,</p>
        <p>Thank you for choosing Prime Booking! Your reservation at <strong>{hotel_name}</strong> has been successfully confirmed.</p>
        <table style="width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;">
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Property:</td><td style="padding:8px 0;font-weight:600;">{hotel_name}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Room:</td><td style="padding:8px 0;font-weight:600;">{room_type}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Check-in:</td><td style="padding:8px 0;font-weight:600;">{checkin_date}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Check-out:</td><td style="padding:8px 0;font-weight:600;">{checkout_date}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Total Paid:</td><td style="padding:8px 0;font-weight:700;color:#1890ff;">BDT {total_amount}</td></tr>
        </table>
        <p style="font-size:13px;color:#64748b;">Need to modify or cancel? Visit your Prime Booking account dashboard or contact customer support.</p>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        &copy; Prime Booking. All rights reserved.
    </div>
</div>',
                'placeholders' => ['guest_name', 'hotel_name', 'room_type', 'checkin_date', 'checkout_date', 'total_amount', 'booking_ref'],
                'is_active' => true,
            ],
            [
                'code' => 'booking_cancelled',
                'name' => 'Booking Cancellation',
                'category' => 'booking',
                'subject' => 'Reservation Cancelled - #{booking_ref} | Prime Booking',
                'body_html' => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
    <div style="background:#ef4444;color:#ffffff;padding:24px;text-align:center;">
        <h1 style="margin:0;font-size:22px;">Reservation Cancelled</h1>
        <p style="margin:6px 0 0;font-size:14px;opacity:0.9;">Ref: #{booking_ref}</p>
    </div>
    <div style="padding:24px;">
        <p>Dear <strong>{guest_name}</strong>,</p>
        <p>Your booking for <strong>{hotel_name}</strong> (#{booking_ref}) has been cancelled.</p>
        <p>Cancellation Reason: <em>{cancellation_reason}</em></p>
        <p>Refund Amount: <strong>BDT {refund_amount}</strong> (if applicable, credited within 3-5 business days).</p>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        &copy; Prime Booking. All rights reserved.
    </div>
</div>',
                'placeholders' => ['guest_name', 'hotel_name', 'booking_ref', 'cancellation_reason', 'refund_amount'],
                'is_active' => true,
            ],
            [
                'code' => 'payout_approved',
                'name' => 'Vendor Payout Processed',
                'category' => 'payout',
                'subject' => 'Payout Processed - BDT {payout_amount} | Prime Booking Vendor Hub',
                'body_html' => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
    <div style="background:#10b981;color:#ffffff;padding:24px;text-align:center;">
        <h1 style="margin:0;font-size:22px;">Payout Disbursed</h1>
        <p style="margin:6px 0 0;font-size:14px;opacity:0.9;">Ref: #{payout_ref}</p>
    </div>
    <div style="padding:24px;">
        <p>Dear <strong>{vendor_name}</strong>,</p>
        <p>Great news! Your payout request of <strong>BDT {payout_amount}</strong> has been approved and processed to your registered account.</p>
        <table style="width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;">
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Method:</td><td style="padding:8px 0;font-weight:600;">{payment_method}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Account:</td><td style="padding:8px 0;font-weight:600;">{account_details}</td></tr>
            <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;color:#64748b;">Transaction ID:</td><td style="padding:8px 0;font-weight:600;">{transaction_id}</td></tr>
        </table>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        &copy; Prime Booking Vendor Hub.
    </div>
</div>',
                'placeholders' => ['vendor_name', 'payout_amount', 'payout_ref', 'payment_method', 'account_details', 'transaction_id'],
                'is_active' => true,
            ],
            [
                'code' => 'password_reset',
                'name' => 'Password Reset Request',
                'category' => 'auth',
                'subject' => 'Reset Your Prime Booking Password',
                'body_html' => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
    <div style="background:#1890ff;color:#ffffff;padding:24px;text-align:center;">
        <h1 style="margin:0;font-size:22px;">Password Reset Request</h1>
    </div>
    <div style="padding:24px;">
        <p>Hello <strong>{user_name}</strong>,</p>
        <p>You requested a password reset for your Prime Booking account. Click the button below to set a new password:</p>
        <div style="text-align:center;margin:30px 0;">
            <a href="{reset_url}" style="background:#1890ff;color:#ffffff;padding:12px 28px;text-decoration:none;border-radius:6px;font-weight:600;display:inline-block;">Reset Password</a>
        </div>
        <p style="font-size:12px;color:#64748b;">If you did not request this, please ignore this email or secure your account.</p>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        &copy; Prime Booking. All rights reserved.
    </div>
</div>',
                'placeholders' => ['user_name', 'reset_url'],
                'is_active' => true,
            ],
            [
                'code' => 'inquiry_reply',
                'name' => 'Customer Support / Inquiry Reply',
                'category' => 'support',
                'subject' => 'Response to your inquiry: {subject} | Prime Booking',
                'body_html' => '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
    <div style="background:#0f172a;color:#ffffff;padding:24px;text-align:center;">
        <h1 style="margin:0;font-size:22px;">Support Ticket Response</h1>
        <p style="margin:6px 0 0;font-size:14px;opacity:0.9;">Ticket #{ticket_ref}</p>
    </div>
    <div style="padding:24px;">
        <p>Dear <strong>{guest_name}</strong>,</p>
        <p>Our support team has responded to your request regarding: <strong>{subject}</strong></p>
        <div style="background:#f8fafc;border-left:4px solid #1890ff;padding:16px;margin:16px 0;font-size:14px;line-height:1.6;">
            {reply_message}
        </div>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        &copy; Prime Booking Support.
    </div>
</div>',
                'placeholders' => ['guest_name', 'subject', 'ticket_ref', 'reply_message'],
                'is_active' => true,
            ]
        ];

        foreach ($defaults as $item) {
            EmailTemplate::create($item);
        }
    }

    public function index(Request $request)
    {
        $this->ensureDefaultsExist();

        $query = EmailTemplate::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $templates = $query->orderBy('category')->orderBy('name')->get();

        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email-templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $template->update($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully!');
    }

    public function preview($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        // Dummy data for rendering preview
        $dummy = [
            'guest_name' => 'John Doe',
            'hotel_name' => 'Grand Palace Hotel Dhaka',
            'room_type' => 'Deluxe King Suite',
            'checkin_date' => date('d M Y', strtotime('+3 days')),
            'checkout_date' => date('d M Y', strtotime('+6 days')),
            'total_amount' => '14,500',
            'booking_ref' => 'PB-789012',
            'cancellation_reason' => 'Guest schedule change request',
            'refund_amount' => '12,500',
            'vendor_name' => 'Rahim Chowdhury',
            'payout_amount' => '45,000',
            'payout_ref' => 'PAY-4091',
            'payment_method' => 'Bank Transfer (BRAC Bank)',
            'account_details' => 'A/C: **** 1098',
            'transaction_id' => 'TXN98721360',
            'user_name' => 'John Doe',
            'reset_url' => url('/password/reset/dummy-token'),
            'ticket_ref' => 'TKT-2026-0042',
            'subject' => 'Room check-in time extension inquiry',
            'reply_message' => 'Hello! We have confirmed with the property front-desk and your late check-in request until 11:00 PM has been granted with no extra surcharge. Have a wonderful stay!',
        ];

        $rendered = $template->render($dummy);

        return response()->json([
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
        ]);
    }

    public function toggle($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        return back()->with('success', 'Template status updated.');
    }
}
