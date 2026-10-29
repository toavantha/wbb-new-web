<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(12);

        return view('frontend.employees', compact('employees'));
    }
}
