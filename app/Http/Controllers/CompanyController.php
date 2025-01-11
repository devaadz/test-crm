<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') {
            $companies = Company::query();

            if ($request->filled('search')) {
                $companies->where('name', 'LIKE', "%{$request->search}%");
            }
            
            return $companies->paginate(10);
        }
        if ($user->role === 'manager') {
            $companies = Company::where('id', $user->company_id)->get();
            return response()->json($companies);
        }
        return response()->json(['error' => 'Unauthorized.company '], 403);
    }

    public function store(Request $request)
    {
        try{
            $user = auth()->user();

            if ($user->role === 'super_admin') {
                $request->validate([
                                'name' => 'required|unique:companies',
                                'email' => 'required|email|unique:companies',
                                'password' => 'required',
                                'phone' => 'required',
                            ]);
                                // Logika jika kondisi terpenuhi
                $company = Company::create($request->all());
                $manager = User::create([
                    'name' => 'Manager ' . $company->name,
                    'email' => 'manager.' . strtolower($company->email),
                    'password' => bcrypt($company->password),
                    'role' => 'manager',
                    'company_id' => $company->id,
                ]);
            
                return response()->json([
                    'message' => 'Company created successfully',
                    'data' => $company,
                    'data manager' => $manager,
                ], 201);
            }
            return response()->json(['error' => 'Unauthorized.company '], 403);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
