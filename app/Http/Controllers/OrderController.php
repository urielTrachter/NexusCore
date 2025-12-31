<?php

namespace App\Http\Controllers;

use App\Actions\ProcessOrderAction;
use App\Http\Requests\ProcessOrderRequest;

class OrderController extends Controller
{
   
    protected $processOrderAction;

    /**
     * Inject the ProcessOrderAction dependency.
     *
     * @param ProcessOrderAction $processOrderAction
     */
    public function __construct(ProcessOrderAction $processOrderAction)
    {
        $this->processOrderAction = $processOrderAction;
    }

    public function processOrder(ProcessOrderRequest $request)
    {
        $result = ($this->processOrderAction)($request);

        return response()->json($result);
    }
}