<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Contributor;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getCollections(){
        //Обробка запиту з параметром id для виводу массиву збору з усіма вкладеннями
        if(isset($_GET['id'])){
            $collection = Collection::where('id', $_GET['id'])->first();
            $contributors = Contributor::where('collection_id', $collection->id)->get();
            $data = $collection->toArray();
            $cont_arr = [];


            foreach ($contributors as $contributor){
                array_push($cont_arr, ['user_name' => $contributor->user_name, 'amount' => $contributor->amount]);
            }
            $data += ['contributors' => $cont_arr];
            return response()->json($data);
        }

        $collections = Collection::all();

        //Обробка запиту з параметром filter для виводу массиву зборів у яких сумма вкладень менша за цільову сумму
        if(isset($_GET['filter'])){
            $data = [];
            foreach ($collections as $collection){
                $contributors = Contributor::where('collection_id', $collection->id)->get();
                if($collection->target_amount > $contributors->sum('amount')){
                    array_push($data, $collection->toArray());
                }
            }
            return response()->json($data);
        }
        return response()->json($collections);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCollection(Request $request){
        $collection = new Collection();

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'target_amount' =>'required|numeric',
            'link' => 'required|unique:collections'
        ]);


        $collection->title = $request->title;
        $collection->description = $request->description;
        $collection->target_amount = $request->target_amount;
        $collection->link = $request->link;

        $collection->save();

        return response()->json([
            "message" => "Collection has been added successfully!"
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCollection(Request $request, $id){

        $validated = $request->validate([
            'title' => 'max:255',
            'description' => 'max:255',
            'target_amount' =>'numeric',
            'link' => 'unique:collections'
        ]);

        $collection = Collection::where('id', $id)->first();

        //Перевірка на співпадіння за ідентифікатором
        if(!isset($collection)){
            return response()->json([
                "message" => "Collection not found."
            ]);
        }

        //За допомогою таких перевірок можна посилати запит лише з полями які будуть редагуватися
        if (isset($request->title)){
            $collection->title = $request->title;
        }
        if (isset($request->description)) {
            $collection->description = $request->description;
        }
        if (isset($request->target_amount)) {
            $collection->target_amount = $request->target_amount;
        }
        if (isset($request->link)) {
            $collection->link = $request->link;
        }

        $collection->save();

        return response()->json([
            "message" => "Collection has been updated successfully!"
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCollection($id){
        $collection = Collection::where('id', $id)->first();

        //Перевірка на співпадіння за ідентифікатором
        if(!isset($collection)){
            return response()->json([
                "message" => "Collection not found."
            ]);
        }

        $contributors = Contributor::where('collection_id', $collection->id)->get();

        //видалення усіх вкладень, які належать до вибраного збору перед його видаленням
        foreach ($contributors as $contributor){
            $contributor->delete();
        }

        $collection->delete();

        return response()->json([
           "message" => "Collection has been deleted."
        ]);
    }

}
