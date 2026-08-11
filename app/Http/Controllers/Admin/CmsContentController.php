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
        $pages = CmsContent::orderBy('group')->orderBy('title')->get();
        return view('admin.cms.index', compact('pages'));
    }

    public function edit(CmsContent $cm)
    {
        return view('admin.cms.edit', ['page' => $cm]);
    }

    public function update(Request $request, CmsContent $cm)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $cm->update($validated);
        Cache::forget("cms_content:{$cm->key}");

        return redirect()->route('admin.cms.index')->with('success', "Page '{$cm->title}' updated successfully!");
    }
}
