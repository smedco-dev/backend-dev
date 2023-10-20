<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();
            if ($roles->isEmpty()) {
                return response()->json([
                    'data_role' => [],
                    'response_total' => 0,
                    'response_code' => '00',
                    'response_status' => true,
                    'response_message' => 'Data not found',
                    'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 200);
            }

            $result = [];
            foreach ($roles as $role) {
                $roleData = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'short_name' => $role->short_name,
                    'total_users' => $role->users->count(),
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];

                $result[] = $roleData;
            }

            return response()->json([
                'data_role' => $result,
                'response_total' => count($result),
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Data found',
                'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'short_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors'          => $validator->errors(),
                'response_code'   => '00',
                'response_status' => true,
                'date_request'    => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 400);
        }

        try {
            $data = $request->all();
            $role = Role::create($data);

            return response()->json([
                'data_new_role'    => $role,
                'response_code'    => '00',
                'response_status'  => true,
                'response_message' => 'Role created successfully',
                'date_request'     => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating Role' . $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                return response()->json([
                    'data_role' => [],
                    'response_code' => '01',
                    'response_status' => false,
                    'response_message' => 'Data not found',
                    'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 404);
            }

            $usersWithRole = $role->users()->select('id', 'personal_number', 'name', 'email')->get();
            $result = [
                'id' => $role->id,
                'name' => $role->name,
                'short_name' => $role->short_name,
                'total_users' => $role->users->count(),
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
                'data_user' => $usersWithRole
            ];

            return response()->json([
                'data_role' => $result,
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Data found',
                'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'data_role' => [],
                    'response_code' => '01',
                    'response_status' => false,
                    'response_message' => 'Data not found',
                    'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name,' . $id,
                'short_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors'          => $validator->errors(),
                    'response_code'   => '00',
                    'response_status' => true,
                    'date_request'    => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 400);
            }

            $role->update($request->all());
            return response()->json([
                'data_update_role' => $role,
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Role updated successfully',
                'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'data_role' => [],
                    'response_code' => '01',
                    'response_status' => false,
                    'response_message' => 'Data not found',
                    'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 404);
            }

            $usersWithRole = $role->users;
            if ($usersWithRole->isNotEmpty()) {
                return response()->json([
                    'data_role' => $role,
                    'response_code' => '01',
                    'response_status' => false,
                    'response_message' => 'Role is in use and cannot be deleted',
                    'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ], 400);
            }

            $role->delete();
            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Role deleted successfully',
                'date_request' => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
