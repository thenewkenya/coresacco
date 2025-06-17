<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalsController extends Controller
{
    /**
     * Display member's financial goals
     */
    public function index()
    {
        $user = Auth::user();
        
        $goals = $user->goals()
            ->latest()
            ->get()
            ->groupBy('type');

        // Calculate total progress
        $totalTargetAmount = $user->goals()->sum('target_amount');
        $totalCurrentAmount = $user->goals()->sum('current_amount');
        $overallProgress = $totalTargetAmount > 0 
            ? ($totalCurrentAmount / $totalTargetAmount) * 100 
            : 0;

        // Get upcoming goals
        $upcomingGoals = $user->goals()
            ->where('status', Goal::STATUS_ACTIVE)
            ->where('target_date', '>', now())
            ->orderBy('target_date')
            ->take(3)
            ->get();

        return view('goals.index', compact(
            'goals',
            'overallProgress',
            'upcomingGoals'
        ));
    }

    /**
     * Show create goal form
     */
    public function create()
    {
        $goalTypes = Goal::getTypes();
        return view('goals.create', compact('goalTypes'));
    }

    /**
     * Store new goal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1000',
            'target_date' => 'required|date|after:today',
            'type' => 'required|string|in:' . implode(',', array_keys(Goal::getTypes())),
            'auto_save_amount' => 'nullable|numeric|min:100',
            'auto_save_frequency' => 'nullable|required_with:auto_save_amount|in:weekly,monthly'
        ]);

        $goal = Auth::user()->goals()->create($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Financial goal created successfully!');
    }

    /**
     * Show goal details
     */
    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);

        // Get related transactions if auto-save is enabled
        $transactions = [];
        if ($goal->auto_save_amount) {
            $transactions = Transaction::where('metadata->goal_id', $goal->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('goals.show', compact('goal', 'transactions'));
    }

    /**
     * Show edit goal form
     */
    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);

        $goalTypes = Goal::getTypes();
        return view('goals.edit', compact('goal', 'goalTypes'));
    }

    /**
     * Update goal
     */
    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1000',
            'target_date' => 'required|date|after:today',
            'type' => 'required|string|in:' . implode(',', array_keys(Goal::getTypes())),
            'auto_save_amount' => 'nullable|numeric|min:100',
            'auto_save_frequency' => 'nullable|required_with:auto_save_amount|in:weekly,monthly'
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Financial goal updated successfully!');
    }

    /**
     * Delete goal
     */
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Financial goal deleted successfully!');
    }

    /**
     * Update goal progress
     */
    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $goal->update([
            'current_amount' => $validated['amount']
        ]);

        if ($goal->current_amount >= $goal->target_amount) {
            $goal->update(['status' => Goal::STATUS_COMPLETED]);
            $message = 'Congratulations! You have achieved your financial goal!';
        } else {
            $message = 'Goal progress updated successfully!';
        }

        return back()->with('success', $message);
    }
} 