<?php

namespace App\Http\Controllers;

use App\Actions\ProcessOrderAction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
     */
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

    public function processOrder(Request $request)
    {
        $result = ($this->processOrderAction)($request);

        return response()->json($result);
    }
}