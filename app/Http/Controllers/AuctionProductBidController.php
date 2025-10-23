<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AuctionBidMailManager;
use App\Models\AuctionProductBid;
use App\Models\Product;
use Auth;
use Illuminate\Http\Request;
use Mail;

/**
 * AuctionProductBidController
 * Handles auction product bidding functionality for web routes
 * 
 * @author Mohammad Hassan
 */
class AuctionProductBidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created auction bid in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Mohammad Hassan
        $bid = AuctionProductBid::where('product_id', $request->product_id)->where('user_id', Auth::user()->id)->first();
        if ($bid == null) {
            $bid = new AuctionProductBid;
            $bid->user_id = Auth::user()->id;
        }
        $bid->product_id = $request->product_id;
        $bid->amount = $request->amount;
        
        if ($bid->save()) {
            // Mohammad Hassan - Send notification to second highest bidder
            $secound_max_bid = AuctionProductBid::where('product_id', $request->product_id)->orderBy('amount', 'desc')->skip(1)->first();
            if ($secound_max_bid != null) {
                if ($secound_max_bid->user->email != null) {
                    $product = Product::where('id', $request->product_id)->first();
                    $array['view'] = 'emails.auction_bid';
                    $array['subject'] = translate('Auction Bid');
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['content'] = 'Hi! A new user bidded more then you for the product, ' . $product->name . '. ' . 'Highest bid amount: ' . $bid->amount;
                    $array['link'] = route('auction-product', $product->slug);
                    try {
                        Mail::to($secound_max_bid->user->email)->queue(new AuctionBidMailManager($array));
                    } catch (\Exception $e) {
                        //dd($e->getMessage());
                    }
                }
            }

            flash(translate('Bid Placed Successfully.'))->success();
            return back();
        } else {
            flash(translate('Something Went Wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}