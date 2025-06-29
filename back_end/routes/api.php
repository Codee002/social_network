<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\User\ConversationController;
use App\Http\Controllers\Api\User\PostController;
use App\Http\Controllers\Api\User\RelationController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::apiResource("/test", TestController::class);

// Auth
Route::prefix("/auth")->name("auth.")->group(function () {
    Route::post("register", [AuthController::class, 'register'])->name("register");
    Route::post("login", [AuthController::class, 'login'])->name("login");
    Route::middleware('auth:sanctum')->post("logout", [AuthController::class, 'logout'])->name("logout");
});

// User
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/owner', [UserController::class, 'getOwner']);
    Route::get('/user/{id}', [UserController::class, 'getUser']);
    Route::get('/getRelation/{owner_id}/{user_id}', [UserController::class, 'getRelation']);
    // Route::get('/getFriends/{id}', [UserController::class, 'getFriends']);
    Route::get('/getPosts/{id}', [UserController::class, 'getPosts']);
    Route::get('/getFriends/{id}', [UserController::class, 'getFriends']);
    Route::get('/getInvited/{id}', [UserController::class, 'getInvited']);

    // Relation
    Route::post('/relation/addRelation/', [RelationController::class, 'addRelation']);
    Route::post('/relation/changeRelation/', [RelationController::class, 'changeRelation']);

    // Conversation
    Route::post("conversation/find", [ConversationController::class, "findConversationUser"]);
    Route::get("conversation/get", [ConversationController::class, "getConversation"]);
    Route::get("conversation/message/{conversation_id}", [ConversationController::class, "getMessage"]);
    Route::post("conversation/sendMessage", [ConversationController::class, "sendMessage"]);

    // Post
    Route::post("post/store", [PostController::class, "store"]);
    Route::get("post/detail/{post_id}", [PostController::class, "getPostDetail"]);
    Route::post("post/storeComment/", [PostController::class, "storeComment"]);
    Route::post("post/storeLike/", [PostController::class, "storeLike"]);
    Route::post("post/storeView/", [PostController::class, "storeView"]);
    Route::post("post/storeShare/", [PostController::class, "storeShare"]);

});
