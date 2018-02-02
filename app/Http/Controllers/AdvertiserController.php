<?php

namespace App\Http\Controllers;

use App\Advertiser;
use App\FoundationAttribute;
use App\Providers\MutableDataProcessorProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AdvertiserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('advertisers.index', [
            "advertisers" => Advertiser::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('advertisers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create the base advertiser model.
        $advertiser = Advertiser::create([
            "owner_user_id" => Auth::user()->id,
            "name" => Input::get("name"),
            "email" => Input::get("email")
        ]);

        // Run through each attribute sent.
        foreach (collect(Input::get("attributes"))->toArray() as $item => $value) {
            // Update if existing.
            if (FoundationAttribute::whereFoundationParentId($advertiser->id)->whereName($item)->exists()) {
                (new MutableDataProcessorProvider())->updateDataPair(FoundationAttribute::whereFoundationParentId($advertiser->id)->whereName($item)->first()->storage_id, $value);
            } else {
                $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($advertiser->id, "advertiser", $value);
                FoundationAttribute::create([
                    "foundation_parent_id" => $advertiser->id,
                    "name" => $item,
                    "storage_id" => $storageItem->id
                ]);
            }
        }

        return redirect(route("advertisers.show", ["id" => $advertiser->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $advertiser = Advertiser::whereId($id)->first();
        $financials = [
            "revenue" => 0,
            "profit" => 0
        ];
        foreach($advertiser->campaigns as $campaign) {
            $financials["revenue"] += $campaign->reports->sum('revenue');
            $financials["profit"] += ($campaign->reports->sum('revenue') - $campaign->reports->sum('payout'));
        }
        return view('advertisers.show', [
            'advertiser' => $advertiser,
            'financials' => $financials
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('advertisers.edit', [
            'advertiser' => Advertiser::whereId($id)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Advertiser::whereId($id)->update([
            "name" => Input::get("name"),
            "email" => Input::get("email")
        ]);

        // Run through each attribute sent.
        foreach (collect(Input::get("attributes"))->toArray() as $item => $value) {
            // Update if existing.
            if (FoundationAttribute::whereFoundationParentId($id)->whereName($item)->exists()) {
                (new MutableDataProcessorProvider())->updateDataPair(FoundationAttribute::whereFoundationParentId($id)->whereName($item)->first()->storage_id, $value);
            } else {
                $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($id, "advertiser", $value);
                FoundationAttribute::create([
                    "foundation_parent_id" => $id,
                    "name" => $item,
                    "storage_id" => $storageItem->id
                ]);
            }
        }

        return redirect(route("advertisers.show",["id" => $id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
