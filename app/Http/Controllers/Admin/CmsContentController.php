<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;
use Illuminate\Support\Facades\Cache;

class CmsContentController extends Controller
{
    public function index()
    {
        // Seed sample CMS pages if empty
        if (CmsContent::count() === 0) {
            CmsContent::create([
                'key'     => 'about_us',
                'title'   => 'About PRIME BOOKING',
                'group'   => 'general',
                'content' => '<h1>About Us</h1><p>PRIME BOOKING is Bangladesh\'s premier luxury hotel, resort, and tour package booking platform.</p>',
            ]);
            CmsContent::create([
                'key'     => 'terms_conditions',
                'title'   => 'Terms & Conditions',
                'group'   => 'legal',
                'content' => '<h1>Terms & Conditions</h1><p>Please read these terms carefully before booking any services on PRIME BOOKING.</p>',
            ]);
            CmsContent::create([
                'key'     => 'privacy_policy',
                'title'   => 'Privacy Policy',
                'group'   => 'legal',
                'content' => '<h1>Privacy Policy</h1><p>We respect your privacy and protect your personal information.</p>',
            ]);
            CmsContent::create([
                'key'     => 'host_property',
                'title'   => 'Host Your Property',
                'group'   => 'partner',
                'content' => '<h1>Host Your Hotel or Resort</h1><p>Reach millions of domestic and international travelers.</p>',
            ]);
        }

        $pages = CmsContent::orderBy('group')->orderBy('title')->get();
        return view('admin.cms.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key'     => 'required|string|max:100|unique:cms_contents,key',
            'title'   => 'required|string|max:255',
            'group'   => 'required|string|max:100',
            'content' => 'nullable|string',
        ]);

        CmsContent::create($validated);

        return back()->with('success', "New CMS page '{$validated['title']}' created successfully!");
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function show(CmsContent $cm)
    {
        return view('admin.cms.edit', ['page' => $cm]);
    }

    public function edit(CmsContent $cm)
    {
        return view('admin.cms.edit', ['page' => $cm]);
    }

    public function update(Request $request, CmsContent $cm)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'group'   => 'nullable|string|max:100',
            'content' => 'nullable|string',
        ]);

        $cm->update($validated);
        Cache::forget("cms_content:{$cm->key}");

        return redirect()->route('admin.cms.index')->with('success', "Page '{$cm->title}' updated successfully!");
    }

    public function destroy(CmsContent $cm)
    {
        $cm->delete();
        Cache::forget("cms_content:{$cm->key}");

        return back()->with('success', "CMS page '{$cm->title}' deleted successfully!");
    }
}
