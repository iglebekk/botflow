<?php

return [
    'navigation' => [
        'bots' => 'Bots',
    ],

    'index' => [
        'title' => 'Bots',
        'subtitle' => 'Manage the bots your team can assign work to.',
        'create' => 'Add bot',
        'empty' => 'No bots have been added yet.',
        'assigned_tasks' => ':count assigned tasks',
        'created_tasks' => ':count created tasks',
        'show' => 'Open bot',
    ],

    'create' => [
        'title' => 'Add bot',
        'subtitle' => 'Register a bot so it can receive tasks and API tokens.',
    ],

    'show' => [
        'title' => 'Bot details',
        'back' => 'Back to bots',
        'summary' => 'Summary',
        'tokens' => 'Access tokens',
        'danger_zone' => 'Danger zone',
        'new_token' => 'Issue token',
        'token_name' => 'Token name',
        'token_name_placeholder' => 'Production worker',
        'plain_text_token_title' => 'Copy this token now',
        'plain_text_token_help' => 'This is the only time the plain text token will be shown. Store it safely and share it with the bot runtime.',
        'slug' => 'Slug',
        'status' => 'Status',
        'description' => 'Description',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'last_seen' => 'Last seen',
        'never_seen' => 'Never',
        'no_tokens' => 'No tokens have been created yet.',
        'token_last_used' => 'Last used',
        'token_never_used' => 'Never used',
        'created_at' => 'Created',
        'delete' => 'Delete bot',
        'delete_help' => 'Deleting a bot revokes its tokens and removes it from active assignment, while keeping historical tasks and comments readable.',
        'delete_confirm' => 'Delete this bot? Existing tasks and comments will keep historical references, but the bot will no longer be active.',
    ],

    'form' => [
        'name' => 'Name',
        'description' => 'Description',
        'active' => 'Bot is active',
        'save' => 'Save bot',
        'cancel' => 'Cancel',
    ],

    'messages' => [
        'created' => 'Bot created successfully.',
        'deleted' => 'Bot deleted successfully.',
        'token_created' => 'Bot token created successfully.',
    ],

    'history' => [
        'deleted_name' => ':name (deleted bot)',
    ],
];
