<?php

namespace App\Policies;

use App\Models\Bot;
use App\Models\User;

class BotPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Bot $bot): bool
    {
        return $bot->owner->is($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function issueToken(User $user, Bot $bot): bool
    {
        return $this->view($user, $bot);
    }

    public function delete(User $user, Bot $bot): bool
    {
        return $this->view($user, $bot);
    }
}
