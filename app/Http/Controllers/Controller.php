<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * Register middleware directly in controller
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }
}

class ControllerMiddlewareOptions
{
    protected $options;

    public function __construct(array &$options)
    {
        $this->options = &$options;
    }

    public function except($methods)
    {
        $this->options['except'] = array_merge($this->options['except'] ?? [], (array) $methods);
        return $this;
    }

    public function only($methods)
    {
        $this->options['only'] = array_merge($this->options['only'] ?? [], (array) $methods);
        return $this;
    }
}