<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ApiNotFoundException extends Exception
{
    protected string $resource;
    protected string $action;

    public function __construct(
        string $resource,
        string $action = 'find',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->resource = class_basename($resource);
        $this->action = $action;

        parent::__construct();
    }

    public function render(Request $request)
    {
        return response()->json([
            'status' => false,
            'message' => "Couldn't {$this->action} {$this->resource}"
        ], 404);
    }
}
