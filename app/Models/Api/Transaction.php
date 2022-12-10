<?php

namespace App\Models\Api;

use App\Services\FilterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function getAllTransactions($request) :array
    {
        $path = storage_path('providers');
        $files = array_diff(scandir($path), array('.', '..'));
        $transactions = [];
        foreach ($files as $file){
            $transactions[pathinfo($file, PATHINFO_FILENAME)] = json_decode(file_get_contents($path.'/'.$file), true);
        }
        return $this->filter($transactions, $request) ?? $transactions;
    }

    public function filter($transactionsArray, $filters) :array
    {
        $finalResult = null;
        // filter by Providers
        if(isset($filters['provider'])){
            $finalResult[$filters['provider']] = FilterService::providerFilter($filters['provider'], $transactionsArray);
        }

        //filter by statusCode
        if(isset($filters['statusCode'])){
            $finalResult = FilterService::statusCodeFilter($filters['statusCode'], $finalResult, $transactionsArray);
        }

        //filter by Amount here there is 3 scenarios because it is not mentioned in the doc.
        // Scenarion 1 : min amount is the only sent filter in this case we are getting everything above min amount
        // Scenarion 2 : max amount is the only sent filter in this case we are getting everything below max amount
        // Scenarion 3 : min & max amounts are sent in this case we are getting everything in between these values
        if(isset($filters['amounteMin']) || isset($filters['amounteMax'])){
            $tmpArr = (isset($finalResult)) ? $finalResult : $transactionsArray;
            $finalResult = FilterService::amountFilter($tmpArr, $filters['amounteMin'], $filters['amounteMax']);
        }

        //filter by Currency
        if(isset($filters['currency'])){
            $finalResult = FilterService::currencyFilter($filters['currency'], $finalResult);
        }

        return array_filter($finalResult);
    }
}
