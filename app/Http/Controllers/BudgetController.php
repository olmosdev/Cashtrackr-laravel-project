<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

#[Middleware("auth")]
#[Middleware("verified")]
class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Auth::user()->budgets()->get();
        return view("dashboard", ["budgets" => $budgets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("budgets.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BudgetRequest $request)
    {
        // $data = $request->validated();
        // dd(auth()->user()->id); This is a helper

        // $user_id = Auth::id(); // This a other way to retrieve the user id
        
        // $budget = Budget::create([
        //     'name' => $data["name"],
        //     'amount' => $data["amount"],
        //     'type' => $data["type"],
        //     'user_id' => $user_id,
        // ]);

        // Using Eloquent Relationships to create a budget for the authenticated user
        // $budget = Auth::user()->budgets()->create($data);
        $budget = Auth::user()->budgets()->create($request->validated());


        return redirect()->route("dashboard");
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        //
    }
}
