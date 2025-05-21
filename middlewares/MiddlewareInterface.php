<?php
namespace Middlewares;

interface MiddlewareInterface {
    /**
     * Handle an incoming request.
     *
     * @param mixed $request The request data (can be an array or object).
     * @param callable $next The next middleware or controller action to call.
     * @return mixed The response from the middleware or controller.
     */
    public function handle($request, callable $next);
}
?>
