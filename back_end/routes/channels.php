<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    Log::info('Broadcast Auth: ' . $user->id . ' vs ' . $id);
    return (int) $user->id === (int) $id;
});

Broadcast::channel('test.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
