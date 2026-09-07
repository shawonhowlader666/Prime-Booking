<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function tasks(Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $tasks = HousekeepingTask::with(['room.roomType', 'assignedTo'])
            ->where('hotel_id', $hotelId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    public function assignTask(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $task = HousekeepingTask::where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();

        $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);

        $task->assigned_to_user_id = $request->assigned_to_user_id;
        $task->status = 'IN_PROGRESS';
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping task assigned successfully',
            'data' => $task->load('assignedTo'),
        ]);
    }

    public function completeTask(int $id, Request $request): JsonResponse
    {
        $hotelId = $request->user()->hotel_id ?? 1;

        $task = HousekeepingTask::with('room')->where('hotel_id', $hotelId)->where('id', $id)->firstOrFail();

        $task->status = 'COMPLETED';
        $task->completed_at = now();
        $task->save();

        if ($task->room) {
            $task->room->status = 'AVAILABLE';
            $task->room->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Room {$task->room?->room_number} marked CLEAN and AVAILABLE",
            'data' => $task,
        ]);
    }
}
