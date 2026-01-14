<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Обработка кастомных исключений
        $exceptions->render(function (\App\Exceptions\NewsNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'news_not_found',
                ], 404);
            }
        });

        $exceptions->render(function (\App\Exceptions\VideoPostNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'video_post_not_found',
                ], 404);
            }
        });

        $exceptions->render(function (\App\Exceptions\CommentNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'comment_not_found',
                ], 404);
            }
        });

        $exceptions->render(function (\App\Exceptions\UserNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'user_not_found',
                ], 404);
            }
        });

        // Обработка стандартных ModelNotFoundException (на случай, если что-то пропустили)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());
                $id = $e->getIds()[0] ?? 'unknown';
                
                $message = match ($model) {
                    'News' => "Новость с ID {$id} не найдена",
                    'VideoPost' => "Видео пост с ID {$id} не найден",
                    'Comment' => "Комментарий с ID {$id} не найден",
                    'User' => "Пользователь с ID {$id} не найден",
                    default => "Ресурс не найден",
                };
                
                return response()->json([
                    'message' => $message,
                    'error' => 'not_found',
                ], 404);
            }
        });
    })->create();
