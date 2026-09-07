<?php

namespace App\Services;

use App\Models\HousekeepingChecklist;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HousekeepingService
{
    /**
     * Get paginated cleaning tasks for a hotel.
     */
    public function getTasks(int $hotelId, ?string $search = null, ?string $status = null, int $perPage = 20): LengthAwarePaginator
    {
        return HousekeepingTask::with(['room.roomType', 'assignedTo'])
            ->where('hotel_id', $hotelId)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"))
                  ->orWhereHas('assignedTo', fn($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Assign room to housekeeper and create task.
     */
    public function assignTask(int $hotelId, array $data): HousekeepingTask
    {
        return DB::transaction(function () use ($hotelId, $data) {
            $roomId = $data['room_id'];
            $room = Room::where('hotel_id', $hotelId)->findOrFail($roomId);

            $task = HousekeepingTask::create([
                'hotel_id' => $hotelId,
                'room_id' => $room->id,
                'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
                'priority' => $data['priority'] ?? 'MEDIUM',
                'status' => 'IN_PROGRESS',
                'notes' => $data['notes'] ?? 'Daily room cleaning',
            ]);

            $room->cleanliness = 'CLEANING';
            $room->save();

            AuditService::log($hotelId, 'HOUSEKEEPING_ASSIGNED', $task);

            return $task;
        });
    }

    /**
     * Mark task completed and update room to clean.
     */
    public function completeTask(int $hotelId, int $taskId): HousekeepingTask
    {
        return DB::transaction(function () use ($hotelId, $taskId) {
            $task = HousekeepingTask::where('hotel_id', $hotelId)->findOrFail($taskId);
            $task->status = 'COMPLETED';
            $task->completed_at = now();
            $task->save();

            $room = Room::where('hotel_id', $hotelId)->find($task->room_id);
            if ($room) {
                $room->cleanliness = 'CLEAN';
                $room->save();
            }

            AuditService::log($hotelId, 'HOUSEKEEPING_COMPLETED', $task);

            return $task;
        });
    }

    /**
     * Get all checklist items for hotel.
     */
    public function getChecklists(int $hotelId, ?string $type = null): Collection
    {
        return HousekeepingChecklist::where('hotel_id', $hotelId)
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->get();
    }

    /**
     * Create checklist item.
     */
    public function createChecklist(int $hotelId, array $data): HousekeepingChecklist
    {
        $data['hotel_id'] = $hotelId;
        $item = HousekeepingChecklist::create($data);

        AuditService::log($hotelId, 'CHECKLIST_CREATED', $item);

        return $item;
    }

    /**
     * Update checklist item.
     */
    public function updateChecklist(int $hotelId, int $id, array $data): HousekeepingChecklist
    {
        $item = HousekeepingChecklist::where('hotel_id', $hotelId)->findOrFail($id);
        $item->update($data);

        AuditService::log($hotelId, 'CHECKLIST_UPDATED', $item);

        return $item;
    }

    /**
     * Delete checklist item.
     */
    public function deleteChecklist(int $hotelId, int $id): bool
    {
        $item = HousekeepingChecklist::where('hotel_id', $hotelId)->findOrFail($id);
        $item->delete();

        return true;
    }

    /**
     * Generate Housekeeping Performance Staff Report.
     */
    public function getStaffReport(int $hotelId): Collection
    {
        $staff = User::where('hotel_id', $hotelId)
            ->whereIn('role', ['HOUSEKEEPING', 'STAFF', 'HOTEL_OWNER', 'FRONT_DESK'])
            ->get();

        return $staff->map(function ($user) use ($hotelId) {
            $completed = HousekeepingTask::where('hotel_id', $hotelId)
                ->where('assigned_to_user_id', $user->id)
                ->where('status', 'COMPLETED')
                ->count();

            $pending = HousekeepingTask::where('hotel_id', $hotelId)
                ->where('assigned_to_user_id', $user->id)
                ->where('status', 'PENDING')
                ->count();

            $inProgress = HousekeepingTask::where('hotel_id', $hotelId)
                ->where('assigned_to_user_id', $user->id)
                ->where('status', 'IN_PROGRESS')
                ->count();

            return (object)[
                'id' => $user->id,
                'name' => $user->name,
                'completed' => $completed,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'total' => $completed + $pending + $inProgress,
            ];
        });
    }
}
