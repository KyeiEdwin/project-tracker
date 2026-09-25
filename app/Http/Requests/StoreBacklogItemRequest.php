<?php

namespace App\Http\Requests;

use App\Models\BacklogItem;
use App\Rules\SameProject;
use Illuminate\Validation\Rule;

class StoreBacklogItemRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        $projectId = $this->input('project_id');

        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:backlog_items,id',
                new SameProject('backlog_items', $projectId, 'parent item'),
                function ($attribute, $value, $fail) {
                    if ($value && $projectId = $this->input('project_id')) {
                        $this->validateParentHierarchy($value, $projectId, $fail);
                    }
                },
            ],
            'sprint_id' => [
                'nullable',
                'integer',
                'exists:sprints,id',
                new SameProject('sprints', $projectId, 'sprint'),
            ],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['epic', 'feature', 'story', 'bug', 'spike', 'task'])],
            'points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => ['required', 'string', Rule::in(['backlog', 'ready', 'in-progress', 'done'])],
            'rank' => ['nullable', 'integer'],
        ];
    }

    /**
     * Validate parent-child type hierarchy rules
     */
    protected function validateParentHierarchy($parentId, $projectId, $fail): void
    {
        $parent = BacklogItem::find($parentId);
        
        if (!$parent) {
            return;
        }

        $childType = $this->input('type');
        $parentType = $parent->type;

        // Define valid parent-child type combinations
        $validHierarchy = [
            'epic' => ['feature', 'story', 'bug', 'spike'],
            'feature' => ['story', 'task', 'bug', 'spike'],
            'story' => ['task', 'bug'],
        ];

        // Epics cannot have parents
        if ($childType === 'epic') {
            $fail('Epics cannot have a parent item.');
            return;
        }

        // Check if this parent type can have this child type
        if (!isset($validHierarchy[$parentType]) || !in_array($childType, $validHierarchy[$parentType])) {
            $fail("A {$childType} cannot be a child of a {$parentType}.");
        }
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'The selected parent item does not exist.',
            'sprint_id.exists' => 'The selected sprint does not exist.',
            'type.in' => 'The type must be one of: epic, feature, story, bug, spike, or task.',
            'points.min' => 'Story points cannot be negative.',
            'points.max' => 'Story points cannot exceed 100.',
        ];
    }
}
