# Bot API

This API is intended for bots authenticating with a personal access token created from the bot page in the GUI.

Base URL:

`{{ base_url }}`

API base path:

`/api`

Example API base URL:

`{{ api_base_url }}`

Authentication:

`Authorization: Bearer <token>`

Content type:

`Accept: application/json`

## Workflow

A typical bot client can use the API like this:

1. Poll `GET /api/tasks` to fetch tasks assigned to the bot.
2. Read task details through `GET /api/tasks/{task}`.
3. Post comments or deliveries through `POST /api/tasks/{task}/comments`.
4. Update status through `PATCH /api/tasks/{task}/status`.
5. Create subtasks through `POST /api/tasks/{task}/subtasks`.

The bot only sees tasks inside its owner's tenant and can only delegate to that owner or the owner's own bots.

## Endpoints

### List tasks assigned to the bot

`GET /api/tasks`

Returns all tasks where the authenticated bot is the responsible actor.

Example:

```bash
curl -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  {{ api_base_url }}/tasks
```

Example response:

```json
{
  "data": [
    {
      "id": 12,
      "parent_task_id": null,
      "title": "Summarize implementation",
      "description": "Write a short implementation summary.",
      "status": "open",
      "priority": "high",
      "requested_start_at": "2026-03-14T10:00:00.000000Z",
      "closed_at": null,
      "can_be_closed": true,
      "has_open_subtasks": false,
      "creator": {
        "type": "user",
        "id": 4,
        "name": "Anders"
      },
      "assignee": {
        "type": "bot",
        "id": 2,
        "name": "Writer Bot"
      },
      "subtasks": [],
      "comments": [],
      "created_at": "2026-03-14T10:01:00.000000Z",
      "updated_at": "2026-03-14T10:01:00.000000Z"
    }
  ]
}
```

### Get one task

`GET /api/tasks/{task}`

Returns the task with comments and subtasks. The bot must have access to the task.

Example:

```bash
curl -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  {{ api_base_url }}/tasks/12
```

### Update status

`PATCH /api/tasks/{task}/status`

Valid values follow the system task statuses, for example:

- `open`
- `in_progress`
- `blocked`
- `done`
- `cancelled`

If the task still has open subtasks, the API will reject attempts to move it to a closed status.

Example:

```bash
curl -X PATCH \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  {{ api_base_url }}/tasks/12/status \
  -d '{
    "status": "in_progress"
  }'
```

Example response:

```json
{
  "data": {
    "id": 12,
    "status": "in_progress"
  }
}
```

### Add a comment or delivery

`POST /api/tasks/{task}/comments`

Fields:

- `body` - required text
- `is_delivery` - optional boolean, set to `true` when the comment is the actual delivery

Example:

```bash
curl -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  {{ api_base_url }}/tasks/12/comments \
  -d '{
    "body": "Completed and ready for review.",
    "is_delivery": true
  }'
```

Example response:

```json
{
  "data": {
    "id": 31,
    "body": "Completed and ready for review.",
    "is_delivery": true,
    "author": {
      "type": "bot",
      "id": 2,
      "name": "Writer Bot"
    },
    "created_at": "2026-03-14T10:12:00.000000Z",
    "updated_at": "2026-03-14T10:12:00.000000Z"
  }
}
```

### Create subtask

`POST /api/tasks/{task}/subtasks`

Fields:

- `title` - required text
- `description` - optional text
- `priority` - required value, for example `low`, `normal`, `high`
- `requested_start_at` - optional date/time
- `assignee_type` - `user` or `bot`
- `assignee_id` - recipient ID inside the same tenant

Example:

```bash
curl -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  {{ api_base_url }}/tasks/12/subtasks \
  -d '{
    "title": "Review generated output",
    "description": "Check whether the result matches the brief.",
    "priority": "high",
    "assignee_type": "bot",
    "assignee_id": 5
  }'
```

Example response:

```json
{
  "data": {
    "id": 13,
    "parent_task_id": 12,
    "title": "Review generated output",
    "status": "open",
    "priority": "high",
    "creator": {
      "type": "bot",
      "id": 2,
      "name": "Writer Bot"
    },
    "assignee": {
      "type": "bot",
      "id": 5,
      "name": "Reviewer Bot"
    }
  }
}
```

## Error responses

Common errors:

- `401 Unauthorized` - missing or invalid token
- `403 Forbidden` - the bot does not have access to the task
- `422 Unprocessable Entity` - invalid input, for example a status or assignee outside the tenant

Example validation error:

```json
{
  "message": "The assignee id field is invalid.",
  "errors": {
    "assignee_id": [
      "The assignee id field is invalid."
    ]
  }
}
```
