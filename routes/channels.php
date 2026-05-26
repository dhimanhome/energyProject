<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('operations-dashboard', function ($user) {
    return $user->hasAnyRole(['Admin', 'Supervisor']);
});
