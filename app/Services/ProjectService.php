<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAssigned;
use App\Traits\LogsActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProjectService
{
    use LogsActivity;

    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([
                'name' => $data['name'],
                'client_id' => $data['client_id'],
                'status' => $data['status'],
                'deadline' => $data['deadline'] ?? null,
                'budget' => $data['budget'] ?? null,
                'description' => $data['description'] ?? null,
                'active' => $data['active'] ?? false,
                'user_id' => $data['user_id'] ?? null,
            ]);

            // Create Kanban board
            $board = $project->kanbanBoard()->create(['name' => $project->name . ' Board']);
            $board->createDefaultColumns();

            $this->logActivity('Created Project', 'Created project: ' . $project->name);

            // Handle Attachments
            if (isset($data['attachments'])) {
                $this->handleAttachments($project, $data['attachments']);
            }

            // Sync Assignees (Team)
            if (isset($data['assignees'])) {
                $this->syncAssignees($project, $data['assignees']);
            }

            return $project;
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'name' => $data['name'],
                'client_id' => $data['client_id'],
                'status' => $data['status'],
                'deadline' => $data['deadline'] ?? null,
                'budget' => $data['budget'] ?? null,
                'description' => $data['description'] ?? null,
                'active' => $data['active'] ?? false,
                'user_id' => $data['user_id'] ?? null,
            ]);

            // Handle Attachments
            if (isset($data['attachments'])) {
                $this->handleAttachments($project, $data['attachments']);
            }

            // Sync Assignees (Team)
            if (isset($data['assignees'])) {
                $this->syncAssignees($project, $data['assignees']);
            }

            return $project;
        });
    }

    public function deleteProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $name = $project->name;
            $project->delete();
            $this->logActivity('Deleted Project', 'Deleted project: ' . $name);
        });
    }

    protected function handleAttachments(Project $project, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store('attachments', 'public');

                $project->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }

    protected function syncAssignees(Project $project, array $assigneeIds): void
    {
        $assigneeIds = array_filter($assigneeIds);

        // Notify only new assignees
        $currentIds = $project->users()->pluck('user_id')->toArray();
        $newIds = array_diff($assigneeIds, $currentIds);

        $project->users()->sync($assigneeIds);

        if (!empty($newIds)) {
            $users = User::whereIn('id', $newIds)->get();
            Notification::send($users, new ProjectAssigned($project));
        }
    }
}
