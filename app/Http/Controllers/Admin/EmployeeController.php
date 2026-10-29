<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class EmployeeController extends Controller
{
    public function index(CmsActivityLogger $activityLogger): View
    {
        $employees = Employee::query()->orderBy('name')->paginate(10);
        $photoUploaders = $activityLogger->latestActorsFor('employee_photo', $employees->pluck('id')->all());

        return view('admin.employees.index', compact('employees', 'photoUploaders'));
    }

    public function create(): View
    {
        return view('admin.employees.create');
    }

    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'name_si' => ['required', 'string', 'max:191'],
            'name_ta' => ['required', 'string', 'max:191'],
            'designation' => ['required', 'string', 'max:191'],
            'designation_si' => ['required', 'string', 'max:191'],
            'designation_ta' => ['required', 'string', 'max:191'],
            'mobile' => ['nullable', 'string', 'regex:/\A0[0-9]{9}\z/'],
            'email' => ['nullable', 'email', 'max:191'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'mobile.regex' => __('site.contact.phone_format_error'),
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employees', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the employee image.');
            }

            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $employee = Employee::create($validated);

        if ($request->hasFile('image')) {
            $activityLogger->record(
                $request,
                'uploaded',
                'employee_photo',
                $employee->id,
                $employee->name,
                'Uploaded employee photo: '.$request->file('image')->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee profile created successfully.');
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'name_si' => ['required', 'string', 'max:191'],
            'name_ta' => ['required', 'string', 'max:191'],
            'designation' => ['required', 'string', 'max:191'],
            'designation_si' => ['required', 'string', 'max:191'],
            'designation_ta' => ['required', 'string', 'max:191'],
            'mobile' => ['nullable', 'string', 'regex:/\A0[0-9]{9}\z/'],
            'email' => ['nullable', 'email', 'max:191'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'mobile.regex' => __('site.contact.phone_format_error'),
        ]);

        unset($validated['image']);

        $uploadedImage = $request->file('image');

        if ($uploadedImage !== null) {
            $imagePath = $request->file('image')->store('employees', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the employee image.');
            }

            $previousImagePath = $employee->image_path;
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $employee->update($validated);

        if ($uploadedImage !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'employee_photo',
                $employee->id,
                $employee->name,
                'Replaced employee photo with: '.$uploadedImage->getClientOriginalName(),
            );

            if (isset($previousImagePath)) {
                if (! Storage::disk('public')->delete($previousImagePath)) {
                    throw new RuntimeException('Unable to remove the previous employee image.');
                }

                $activityLogger->record(
                    $request,
                    'removed',
                    'employee_photo',
                    $employee->id,
                    $employee->name,
                    'Replaced previous employee photo: '.basename($previousImagePath),
                );
            }
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee profile updated successfully.');
    }

    public function destroy(Employee $employee, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $imagePath = $employee->image_path;
        $employeeId = $employee->id;
        $employeeName = $employee->name;
        $employee->delete();

        if ($imagePath !== null && ! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Unable to remove the employee image.');
        }

        if ($imagePath !== null) {
            $activityLogger->record(
                $request,
                'removed',
                'employee_photo',
                $employeeId,
                $employeeName,
                'Removed employee photo: '.basename($imagePath),
            );
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee profile deleted successfully.');
    }
}
