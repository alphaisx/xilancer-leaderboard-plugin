<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Modules\Rank\Http\Controllers\AmbassadorController;

Route::group(['prefix' => 'v1', 'middleware' => 'setlang', 'as' => 'api.'], function () {
    Route::get('/api/search-universities', function (Request $request) {
        $name = $request->query('name');
        //Parse JSON file is at ./world_universities_and_domains.json
        $universities = json_decode(file_get_contents(module_path('Rank', 'Services/Data/world_universities_and_domains.json')), true);
        $results = [];
        foreach ($universities as $university) {
            if (stripos($university['name'], $name) !== false) {
                $results[] = $university;
            }
        }
        return response()->json($results);
    })->name('university.search');
});
