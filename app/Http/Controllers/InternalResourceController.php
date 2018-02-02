<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class InternalResourceController extends Controller
{
    /**
     * Access an internal resource.
     *
     * @param $resource
     * @param $queryKey
     * @param $queryValue
     * @return array
     */
    public function access($resource, $queryKey, $queryValue)
    {
        // Resource classname.
        $className = "\\App\\".$resource;

        // Check to see if the class exists.
        if(class_exists($className)) {
            $query = $className::where($queryKey,$queryValue);
            if($query->exists()) {
                return [
                    "success" => "success",
                    "object" => $query->first()->retrieveFullFormat()
                ];
            } else {
                return [
                    "success" => false,
                    "object" => null,
                    "message" => "Found no objects with specific query."
                ];
            }
        }
    }

    /**
     * Access multiple instances of an internal resource.
     *
     * @param $resource
     * @param $queryKey
     * @param $queryValue
     * @return array
     */
    public function accessMultiple($resource, $queryKey, $queryValue)
    {
        // Resource classname.
        $className = "\\App\\".$resource;

        // Check to see if the class exists.
        if(class_exists($className)) {
            $query = $className::where($queryKey,$queryValue);
            if($query->exists()) {

                // Pagination functionality.
                $totalCount = $query->count();
                $currentPage = Input::get("page") ? Input::get("page") : 0;
                $lastPage = floor($query->count() / 20);

                // TODO: Optimize.
                $builtObjects = [];
                foreach($query->skip($currentPage * 20)->limit(20)->get() as $item) {
                    $builtObjects[] = $item->retrieveFullFormat();
                }

                return [
                    "count" => $totalCount,
                    "currentPage" => $currentPage,
                    "lastPage" => $lastPage,
                    "success" => "success",
                    "objects" => $builtObjects
                ];
            } else {
                return [
                    "success" => false,
                    "object" => null,
                    "message" => "Found no objects with specific query."
                ];
            }
        }
    }
}
