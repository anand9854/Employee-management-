<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'sometimes|string|max:255',
            'department' => 'sometimes|string|max:255',
            'sort_by' => [
                'sometimes',
                Rule::in(['salary', 'joining_date']),
            ],
            'sort_order' => [
                'sometimes',
                Rule::in(['asc', 'desc']),
            ],
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Employee::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('department')) {
            $query->where(
                'department',
                'like',
                '%' . $request->input('department') . '%'
            );
        }

        $sortBy = $request->input('sort_by', 'joining_date');
        $sortOrder = $request->input('sort_order', 'desc');

        $employees = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($request->integer('per_page', 10));

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'department' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0|max:9999999999.99',
            'joining_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::create($validator->validated());

        return response()->json([
            'message' => 'Employee created successfully',
            'data' => $employee,
        ], 201);
    }

    public function show(string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'data' => $employee,
        ]);
    }
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id),
            ],
            'department' => 'sometimes|required|string|max:255',
            'salary' => 'sometimes|required|numeric|min:0|max:9999999999.99',
            'joining_date' => 'sometimes|required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee->update($validator->validated());

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $employee->fresh(),
        ]);
    }
    public function destroy(string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully',
        ]);
    }
}
