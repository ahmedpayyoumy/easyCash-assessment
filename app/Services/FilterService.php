<?php

namespace App\Services;

class FilterService
{
    public function providerFilter($provider, $transactions)
    {
        // list all providers to know if the user sending one in the list or not.
        $keys = array_keys($transactions);
        if (in_array($provider, $keys)) {
            return $transactions[$provider];
        } else {
            return [];
        }
    }

    public function statusCodeFilter($code, $finalResult, $transactions)
    {
        // Map for status codes, and it is like config, and you can put whatever codes you want to the status equal to it
        $codesMap = [
            'paid' => ['done', 1, 100],
            'pending' => ['wait', 2, 200],
            'reject' => ['nope', 3, 300]
        ];
        $codesKeys = array_keys($codesMap);

        //check if we need to filter the big array or the samll one.
        if (in_array($code, $codesKeys)) {
            if ($finalResult) {
                $values = array_values(array_values($finalResult)[0]);
                if (!empty($values) && !in_array($values[3], $codesMap[$code])) {
                    $finalResult = [];
                }
            } else {
                // this is the first way to handle the solution
                $values = array_map(function ($x) use ($codesMap, $code, $finalResult) {
                    $flag = in_array(array_values($x)[3], $codesMap[$code]);
                    if ($flag) {
                        return $x;
                    }
                }, $transactions);
                $finalResult = array_filter($values);

                /*===========================================================================================
                ============ this is a second solution but it will be hashed ================================
                ===========================================================================================*/
                //if(in_array($values[3], $codesMap[$code])){
                //    $finalResult = [];
                //}
                //foreach ($transactionsArray as $array){
                //    $values = array_values($array);
                //    if(in_array($values[3], $codesMap[$code])){
                //        $finalResult[] = $array;
                //    }
                //}
            }
        } else {
            $finalResult = [];
        }
        return $finalResult;
    }

    public function amountFilter($transactions, $min, $max)
    {
        if (isset($min)) {
            // is not multidimensional array which means we have only one record in to filter inside
            if (count($transactions) == count($transactions, COUNT_RECURSIVE)) {
                if (!empty($transactions) && array_values($transactions)[0] >= $min) {
                    $values = $transactions;
                } else {
                    $values = [];
                }
            } else {
                $values = array_map(function ($x) use ($min, $transactions) {
                    $flag = array_values($x)[0] >= $min;
                    if ($flag) {
                        return $x;
                    }
                }, $transactions);
            }
        }
        $transactions = array_filter($values);

        // check the Max Value
        if (isset($max)) {
            // is not multidimensional array which means we have only one record in to filter inside
            if (count($transactions) == count($transactions, COUNT_RECURSIVE)) {
                if (!empty($transactions) && array_values($transactions)[0] <= $max) {
                    $values = $transactions;
                } else {
                    $values = [];
                }
            } else {
                $values = array_map(function ($x) use ($max, $transactions) {
                    $flag = array_values($x)[0] <= $max;
                    if ($flag) {
                        return $x;
                    }
                }, $transactions);
            }
        }
        return $transactions;
    }

    public function currencyFilter($currency, $transactions)
    {
        //check if we need to filter the big array or the samll one.
        if ($transactions && count($transactions) == 1){
            $values = array_values(array_values($transactions)[0]);
            if($values[1] != $currency){
                $transactions = [];
            }
        } else {
            // this is the first way to handle the solution
            $values = array_map(function($x) use ($currency, $transactions){
                $flag = (array_values($x)[1] == $currency);
                if($flag){
                    return $x;
                }
            }, $transactions);
            $transactions = array_filter($values);
        }
        return $transactions;
    }
}
