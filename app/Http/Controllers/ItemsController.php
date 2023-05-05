<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {

            $items = Item::get();

            $items->each(function(&$item) {
                $item->{'title'} = $item->item;
            });

            return collect($items);

        } catch(\Exception $e) {


        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return "something was created";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            $item = new Item();
            $item->item = $request->input('value');
            $item->user_id = auth()->user()->id;
            $item->active = 1;
            $item->completed = 0;
            $item->save();

            return ['value' => $request->input('value'), 'index' => $item->id];

        } catch(\Exception $e) {

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {

        try {

            $item = Item::where('id', $request->input('id'))->first();

            $item->item = $request->input('title');

            $item->save();

            return $request;

        } catch (\Exception $e) {

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        try {

            $itemIds = $request->input('id');

            foreach($itemIds as $key => $itemId) {

                if (is_int($itemId)) {
                    Item::where('id', $itemId)->delete();
                } else {
                    Item::where('id', $itemId['id'])->delete();
                }
            }

            return $itemIds;

        } catch (\Exception $e) {

            return $e;
        }
    }

    public function completed(Request $request)
    {
        try {

            $itemId = $request->input('id');
            $itemCompleted = $request->input('completed');

            $item = Item::where('id', $itemId)->first();

            $item->completed = intval($itemCompleted);

            $item->save();

            return $request;

        } catch (\Exception $e) {

        }
    }
}
