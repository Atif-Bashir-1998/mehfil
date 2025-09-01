<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{conversation_id}', function (User $user, $conversation_id) {
    return $user->conversations()->where('conversations.id', $conversation_id)->exists();
});
