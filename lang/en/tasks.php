<?php

return [
    'navigation' => [
        'dashboard' => 'Overview',
        'tasks' => 'Tasks',
    ],

    'index' => [
        'title' => 'Tasks',
        'subtitle' => 'Track work between humans and bots in one shared queue.',
        'create' => 'Create task',
        'empty' => 'No tasks have been created yet.',
        'subtasks' => ':count subtasks',
        'parent' => 'Subtask of #:id',
    ],

    'dashboard' => [
        'title' => 'Overview',
        'subtitle' => 'Track the current task flow across statuses. Total tasks: :count.',
        'empty_column' => 'No tasks in this step yet.',
        'descriptions' => [
            'open' => 'New work that is ready to be picked up.',
            'in_progress' => 'Tasks currently being worked on.',
            'blocked' => 'Tasks waiting on another dependency or decision.',
            'done' => 'Completed work ready for follow-up or review.',
            'cancelled' => 'Work that has been intentionally stopped or closed.',
        ],
    ],

    'show' => [
        'title' => 'Task details',
        'summary' => 'Summary',
        'comments' => 'Comments',
        'subtasks' => 'Subtasks',
        'new_subtask' => 'Create subtask',
        'status' => 'Status',
        'delivery' => 'Mark this comment as the delivery/result',
        'comment_placeholder' => 'Add context, a handoff note, or the final delivery here.',
        'no_comments' => 'No comments yet.',
        'no_subtasks' => 'No subtasks yet.',
        'created_by' => 'Created by',
        'assigned_to' => 'Assigned to',
        'requested_start_at' => 'Requested start',
        'priority' => 'Priority',
        'parent_task' => 'Parent task',
        'edit' => 'Edit task',
        'back' => 'Back to tasks',
    ],

    'form' => [
        'title' => 'Title',
        'description' => 'Description',
        'priority' => 'Priority',
        'requested_start_at' => 'Requested start time',
        'assignee' => 'Responsible actor',
        'save' => 'Save task',
        'update' => 'Update task',
        'cancel' => 'Cancel',
    ],

    'status_form' => [
        'submit' => 'Update status',
    ],

    'comment_form' => [
        'submit' => 'Add comment',
    ],

    'subtask_form' => [
        'submit' => 'Create subtask',
    ],

    'create' => [
        'title' => 'Create task',
        'subtitle' => 'Create a task and assign it to one human or one bot.',
    ],

    'edit' => [
        'title' => 'Edit task',
        'subtitle' => 'Adjust the task details without changing its history.',
    ],

    'messages' => [
        'created' => 'Task created successfully.',
        'updated' => 'Task updated successfully.',
        'status_updated' => 'Task status updated successfully.',
        'comment_added' => 'Comment added successfully.',
        'subtask_created' => 'Subtask created successfully.',
    ],

    'assignees' => [
        'user_option' => 'Human: :name',
        'bot_option' => 'Bot: :name',
    ],

    'status' => [
        'open' => 'Open',
        'in_progress' => 'In progress',
        'blocked' => 'Blocked',
        'done' => 'Done',
        'cancelled' => 'Cancelled',
    ],

    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    'validation' => [
        'parent_requires_closed_subtasks' => 'All subtasks must be done or cancelled before the parent task can be closed.',
    ],
];
