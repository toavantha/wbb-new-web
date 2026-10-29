<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class BoardMemberController extends Controller
{
    public function index(CmsActivityLogger $activityLogger): View
    {
        $boardMembers = BoardMember::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);
        $photoUploaders = $activityLogger->latestActorsFor('board_member_photo', $boardMembers->pluck('id')->all());

        return view('admin.board-members.index', compact('boardMembers', 'photoUploaders'));
    }

    public function create(): View
    {
        $nextSortOrder = ((int) BoardMember::query()->max('sort_order')) + 1;

        return view('admin.board-members.create', compact('nextSortOrder'));
    }

    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'name_si' => ['required', 'string', 'max:191'],
            'name_ta' => ['required', 'string', 'max:191'],
            'position' => ['required', 'in:chairman,member'],
            'email' => ['nullable', 'email', 'max:191'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['image']);
        $uploadedImage = $request->file('image');

        if ($uploadedImage !== null) {
            $imagePath = $uploadedImage->store('board-members', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the board member photo.');
            }

            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $boardMember = BoardMember::create($validated);
        $activityLogger->record($request, 'created', 'board_member', $boardMember->id, $boardMember->name);

        if ($uploadedImage !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'board_member_photo',
                $boardMember->id,
                $boardMember->name,
                'Uploaded board member photo: '.$uploadedImage->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.board-members.index')->with('success', 'Board member added successfully.');
    }

    public function edit(BoardMember $boardMember): View
    {
        return view('admin.board-members.edit', compact('boardMember'));
    }

    public function update(Request $request, BoardMember $boardMember, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'name_si' => ['required', 'string', 'max:191'],
            'name_ta' => ['required', 'string', 'max:191'],
            'position' => ['required', 'in:chairman,member'],
            'email' => ['nullable', 'email', 'max:191'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['image']);
        $uploadedImage = $request->file('image');
        $previousImagePath = $boardMember->image_path;

        if ($uploadedImage !== null) {
            $imagePath = $uploadedImage->store('board-members', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the board member photo.');
            }

            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $boardMember->update($validated);
        $activityLogger->record($request, 'updated', 'board_member', $boardMember->id, $boardMember->name);

        if ($uploadedImage !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'board_member_photo',
                $boardMember->id,
                $boardMember->name,
                'Replaced board member photo with: '.$uploadedImage->getClientOriginalName(),
            );

            $this->deleteStoredImage($previousImagePath);

            if ($previousImagePath !== null) {
                $activityLogger->record(
                    $request,
                    'removed',
                    'board_member_photo',
                    $boardMember->id,
                    $boardMember->name,
                    'Replaced previous board member photo: '.basename($previousImagePath),
                );
            }
        }

        return redirect()->route('admin.board-members.index')->with('success', 'Board member updated successfully.');
    }

    public function destroy(BoardMember $boardMember, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $boardMemberId = $boardMember->id;
        $boardMemberName = $boardMember->name;
        $imagePath = $boardMember->image_path;

        $this->deleteStoredImage($imagePath);
        $boardMember->delete();

        $activityLogger->record($request, 'deleted', 'board_member', $boardMemberId, $boardMemberName);

        if ($imagePath !== null) {
            $activityLogger->record(
                $request,
                'removed',
                'board_member_photo',
                $boardMemberId,
                $boardMemberName,
                'Removed board member photo: '.basename($imagePath),
            );
        }

        return redirect()->route('admin.board-members.index')->with('success', 'Board member deleted successfully.');
    }

    private function deleteStoredImage(?string $imagePath): void
    {
        if ($imagePath === null || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return;
        }

        if (! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Unable to remove the board member photo.');
        }
    }
}
