<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::select('id', 'name')->get();

        return $this->apiResponse(true, 'roles are retrieved successfully', $roles, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $role_name = $request->name;

        Role::create([
            'name' => $role_name,
            'guard' => 'api'
        ]);

        return $this->apiResponse(true, 'role created successfully', '', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Role::find($id);

        return $this->apiResponse(true, 'role details retrieved successfully', $data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        $data = Role::find($id);

        $new_data = [
            'name' => $request->name
        ];

        $data->update($new_data);

        return $this->apiResponse(true, 'role updated successfully', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
