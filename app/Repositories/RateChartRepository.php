<?php

namespace App\Repositories;

use App\Models\RateChart;
use App\Repositories\Interfaces\RateChartRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;



class RateChartRepository implements RateChartRepositoryInterface
{

    // public function getRateForWeight(float $weight)
    // {
    //     // First, try to find an exact match
    //     $exactMatch = RateChart::where('weight', $weight)->first();
    //     if ($exactMatch) {
    //         return $exactMatch;
    //     }

    //     // If no exact match, find the closest weight ceiling
    //     $closestHigher = RateChart::where('weight', '>=', $weight)
    //         ->orderBy('weight', 'asc')
    //         ->first();

    //     if ($closestHigher) {
    //         return $closestHigher;
    //     }

    //     // If no higher weight found, use the highest available weight
    //     return RateChart::orderBy('weight', 'desc')->first();
    // }


    // public function getRateForWeight(float $weight , int $userId)
    // {
    //     // return RateChart::where('weight', '>=', $weight)
    //     //     ->orderBy('weight', 'asc')
    //     //     ->first() ?? RateChart::orderBy('weight', 'desc')->first();

    // }




    public function getRateForWeight(float $weight, int $userId)
    {

        // ROHIT ka Code hai 
        // return RateChart::whereIn('user_id', [$userId, 0]) // Check user-specific and default rates
        // ->where('weight', '>=', $weight)
        // // ->orderBy('weight', 'asc')
        // ->get();


        // ROHIT ka Code hai 

        /*         $userRates = RateChart::where('user_id', $userId)
            ->where('weight', $weight)
            ->get();
        // dd($userRates->toArray());

        if ($userRates->isNotEmpty()) {
            return $userRates;
        }

        $defaultRates = RateChart::where('user_id', 0)
            ->where('weight', $weight)
            ->get();
        // dd($defaultRates->toArray());

        return $defaultRates; */


        //code is improved 

        $userRates = RateChart::whereIn('user_id', [$userId, 0])
            ->where('weight', $weight)
            ->get();
        return $userRates;





        // ->orderByRaw("FIELD(user_id, $userId, 0)") // Prioritize user-specific rates
        // ->orderBy('weight', 'asc')

        // If no applicable rate is found, return the highest weight rate (fallback)
        // return $rate ?? RateChart::whereIn('user_id', [$userId, 0])
        //     ->orderBy('weight', 'desc')
        //     ->first();

    }
    

    public function getByUserId(int $userId)
    {
        return RateChart::where('user_id', $userId)->get();
    }
    
    public function create(array $data): RateChart
    {
        return RateChart::create($data);
    }

    public function getRates(array $filters)
    {
        $query = RateChart::query();

        // Filter by user_id if provided
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by weight
        if (!empty($filters['weight'])) {
            $query->where('weight', $filters['weight']);
        }

        // Filter by rate amount
        // if (!empty($filters['rate_amount'])) {
        //     $query->where('rate_amount', $filters['rate_amount']);
        // }

        // Filter by created date range
        if (!empty($filters['created_date'])) {
            $dates = explode(' ', $filters['created_date']);

            if (count($dates) === 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));

                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        return $query->get();
    }

    public function findById(int $rateId)
    {
        return RateChart::find($rateId);
    }

    public function updateById(int $rateId, array $updateData): bool
    {
        return RateChart::where('id', $rateId)->update($updateData);
    }    

    public function deleteById(int $rateId): bool
    {
        return RateChart::where('id', $rateId)->delete();
    }
}



// class RateChartRepository implements RateChartRepositoryInterface
// {

// public function getRateForUser(float $weight)
//     // {
//     //     return RateChart::where('weight', '<=', $weight)
//     //         ->orderBy('weight', 'desc')
//     //         ->first();
//     // }


//     public function getRateForUser(float $weight)
//     {
//         // First, try to find an exact match
//         $exactMatch = RateChart::where('weight', $weight)->first();
//         if ($exactMatch) {
//             return $exactMatch;
//         }

//         // If no exact match, find the closest weight ceiling
//         $closestHigher = RateChart::where('weight', '>=', $weight)
//             ->orderBy('weight', 'asc')
//             ->first();

//         if ($closestHigher) {
//             return $closestHigher;
//         }

//         // If no higher weight found, use the highest available weight
//         return RateChart::orderBy('weight', 'desc')->first();
//     }
// }
