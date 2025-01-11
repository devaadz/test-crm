<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'manager') {
            return User::where('company_id', $user->company_id)
                        ->whereIn('role', ['manager', 'employee'])
                        ->paginate(10);
        }

        if ($user->role === 'employee') {
            return User::select('name', 'email', 'company_id','address')
                        ->where('company_id', $user->company_id)
                        ->where('role', 'employee')
                        ->paginate(10);
        }

        return response()->json(['message' => 'Unauthorized employe '], 403);
    }
    
    public function show($id)
    {
        $user = auth()->user();
        if ($user->role === 'manager') {
            if ($id !== null) {
                return User::where('company_id', $user->company_id) 
                             ->findOrFail($id);
            }
            return User::where('company_id', $user->company_id)
                        ->whereIn('role', ['manager', 'employee'])
                        ->paginate(10);
        }

        if ($user->role === 'employee') {
            if ($id) { 
                if ($id == $user->id) {
                    return User::where('company_id', $user->company_id)
                                ->findOrFail($id);; 
                } else {
                    // Mengembalikan data terbatas jika ID milik user lain dalam perusahaan yang sama
                    return User::where('company_id', $user->company_id)
                                ->where('role', 'employee')
                                ->findOrFail($id)
                                ->only(['name', 'email', 'company_id','address']);
                }
            }

        }
        return response()->json(['message' => 'Unauthorized employe '], 403);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($request->role === 'super_admin') {
            return response()->json(['message' => 'Unauthorized '], 403);
        }    
        if ($user->role === 'manager') {
            
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required',
            'address' => 'required',
        ]);
        
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'address' => $request->address,
            'company_id' => $user->company_id,
        ]);

        return response()->json(['message' => 'new'.$employee->role.' successfully added',
                   'data' => $employee], 201);
        }
        return response()->json(['message' => 'Unauthorized employe  '], 403);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->role === 'manager') {
            $employee = User::where('company_id', $user->company_id)
            ->findOrFail($id);
            $request->validate([
                'name' => 'sometimes|required|',
                'email' => 'sometimes|required|email|unique:users',
                'password' => bcrypt('sometimes|required|'),
                'role' => 'sometimes|required|',
                'address' => 'sometimes|required|',
                'phone' => 'sometimes|required|string|max:15',
            ]);
        
            // Ambil hanya data yang diberikan (yang divalidasi)
            $dataToUpdate = $request->only(['name', 'email','password','role','address','phone']);
            $employee->update($dataToUpdate);
            return response()->json($employee, 200);
        }
        if ($user->role === 'employee') {
            if($id == $user->id){
                $employee = User::where('company_id', $user->company_id)
                ->findOrFail($user->id);
                $request->validate([
                    'name' => 'sometimes|required|',
                    'email' => 'sometimes|required|email|unique:users',
                    'password' => bcrypt('sometimes|required|'),
                    'role' => 'sometimes|required|',
                    'address' => 'sometimes|required|',
                    'phone' => 'sometimes|required|string|max:15',
                ]);
            
                // Ambil hanya data yang diberikan (yang divalidasi)
                $dataToUpdate = $request->only(['name', 'email','password','role','address','phone']);
                $employee->update($dataToUpdate);
            }
            return response()->json(['message' => 'Unauthorized employe '], 201);
        }
        
        return response()->json(['message' => 'Unauthorized employe '], 403);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized '], 403);
        }

        $employee = User::where('role', 'employee')
                        ->where('company_id', $user->company_id)
                        ->findOrFail($id);

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }
}
