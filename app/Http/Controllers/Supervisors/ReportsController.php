<?php

namespace App\Http\Controllers\Supervisors;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\Sellers\ReportSubscriptionsRequest;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ReportsController extends Controller
{
    public function sellersSubscriptions(ReportSubscriptionsRequest $request)
    {
        $sellers = Seller::select('id', 'name', 'user_name', 'status')->get();
        $sellers->map(function ($seller) use ($request) {
            $seller['subscriptions'] = $seller->subscriptions()
                ->select(
                    DB::raw('
                        COUNT(*) as `count`, 
                        SUM(capacity) as `capacity`, 
                        SUM(used) as `used`, 
                        SUM(capacity) - SUM(used) as `remaining`,
                        SUM(price) as `price`, 
                        SUM(payed) as `payed`,
                        SUM(price) - SUM(payed) as `debt`
                    ')
                )

                ->where('confirmed_at', '>=', Carbon::create($request->input('start_date')))
                ->where('confirmed_at', '<=', Carbon::create($request->input('end_date')))
                ->where('confirmed_at', '!=', null)
                ->first();
            return $seller;
        });
        return $this->sendData($sellers);
    }
}
