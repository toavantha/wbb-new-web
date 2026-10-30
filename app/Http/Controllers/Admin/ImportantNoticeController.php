<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportantNotice;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportantNoticeController extends Controller
{
    public function index(): View
    {
        $notices = ImportantNotice::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);

        return view('admin.important-notices.index', compact('notices'));
    }

    public function create(): View
    {
        return view('admin.important-notices.create');
    }

    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $this->validateNotice($request);
        $validated['is_active'] = $request->boolean('is_active');

        $notice = ImportantNotice::create($validated);
        $activityLogger->record(
            $request,
            'created',
            'important_notice',
            $notice->id,
            'Important notice #'.$notice->id,
        );

        return redirect()
            ->route('admin.important-information.index')
            ->with('success', 'Important information created successfully.');
    }

    public function edit(int $id): View
    {
        $notice = ImportantNotice::findOrFail($id);

        return view('admin.important-notices.edit', compact('notice'));
    }

    public function update(
        Request $request,
        int $id,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $notice = ImportantNotice::findOrFail($id);
        $validated = $this->validateNotice($request);
        $validated['is_active'] = $request->boolean('is_active');

        $notice->update($validated);
        $activityLogger->record(
            $request,
            'updated',
            'important_notice',
            $notice->id,
            'Important notice #'.$notice->id,
        );

        return redirect()
            ->route('admin.important-information.index')
            ->with('success', 'Important information updated successfully.');
    }

    public function destroy(
        int $id,
        Request $request,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $notice = ImportantNotice::findOrFail($id);
        $notice->delete();
        $activityLogger->record(
            $request,
            'deleted',
            'important_notice',
            $notice->id,
            'Important notice #'.$notice->id,
        );

        return redirect()
            ->route('admin.important-information.index')
            ->with('success', 'Important information deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateNotice(Request $request): array
    {
        return $request->validate([
            'message_en' => ['required', 'string', 'max:500'],
            'message_si' => ['required', 'string', 'max:500'],
            'message_ta' => ['required', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
