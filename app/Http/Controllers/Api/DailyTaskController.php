<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\UserTaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTaskController extends Controller
{
    /**
     * Get all active daily tasks
     */
    public function index(Request $request)
    {
        $tasks = DailyTask::where('is_active', true)
            ->orderBy('coins_reward', 'desc')
            ->get();

        $user = $request->user();
        $today = now()->toDateString();

        // Add completion status for each task
        $tasks->map(function($task) use ($user, $today) {
            $completionsToday = UserTaskCompletion::where('user_id', $user->id)
                ->where('daily_task_id', $task->id)
                ->where('completed_date', $today)
                ->count();

            $task->completed_today = $completionsToday;
            $task->can_complete = $completionsToday < $task->max_completions_per_day;
            $task->remaining_completions = $task->max_completions_per_day - $completionsToday;

            return $task;
        });

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Complete a daily task
     */
    public function complete(Request $request, $taskId)
    {
        $task = DailyTask::find($taskId);

        if (!$task || !$task->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            // Check if user can complete this task today (inside transaction to prevent race condition)
            if (!$task->canBeCompletedByUser($user->id)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Task already completed maximum times today'
                ], 400);
            }

            // Record task completion
            $completion = UserTaskCompletion::create([
                'user_id' => $user->id,
                'daily_task_id' => $task->id,
                'completed_date' => now()->toDateString(),
                'coins_earned' => $task->coins_reward,
            ]);

            // Add coins to user
            $balanceBefore = $user->credit_balance;
            $user->increment('credit_balance', $task->coins_reward);
            $user->refresh();

            // Create transaction record
            $user->transactions()->create([
                'type' => 'credit',
                'amount' => $task->coins_reward,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->credit_balance,
                'source' => 'reward',
                'reference_type' => UserTaskCompletion::class,
                'reference_id' => $completion->id,
                'description' => "Completed daily task: {$task->name}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'data' => [
                    'coins_earned' => $task->coins_reward,
                    'new_balance' => $user->credit_balance,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task'
            ], 500);
        }
    }

    /**
     * Get user's task completion history
     */
    public function history(Request $request)
    {
        $query = $request->user()->taskCompletions()
            ->with('dailyTask')
            ->orderBy('completed_date', 'desc');

        $perPage = $request->get('per_page', 20);
        $history = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Get today's earnings
     */
    public function todayEarnings(Request $request)
    {
        $today = now()->toDateString();

        $earnings = UserTaskCompletion::where('user_id', $request->user()->id)
            ->where('completed_date', $today)
            ->sum('coins_earned');

        return response()->json([
            'success' => true,
            'data' => [
                'today_earnings' => $earnings,
                'date' => $today,
            ]
        ]);
    }
}
