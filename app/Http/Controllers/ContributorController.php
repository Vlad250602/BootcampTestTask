<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Contributor;
use Illuminate\Http\Request;

class ContributorController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addContributor(Request $request){

        $validated = $request->validate([
            'user_name' => 'required|max:255',
            'amount' =>'required|numeric',
            'link' => 'required'
        ]);

        $contributor = new Contributor();

        $collection = Collection::where('link', $request->link)->first();

        //Перевірка на співпадіння за посиланням
        if(!isset($collection)){
            return response()->json([
                "message" => "Collection link is incorrect!"
            ]);
        }

        $contributor->user_name = $request->user_name;
        $contributor->collection_id = $collection->id;
        $contributor->amount = $request->amount;

        $contributor->save();

        return response()->json([
            "message" => "Contributor has been added successfully!"
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateContributor(Request $request, $id){

        $validated = $request->validate([
            'user_name' => 'max:255',
            'amount' =>'numeric',
        ]);

        $contributor = Contributor::where('id', $id)->first();
        //Перевірка на співпадіння за ідентифікатором
        if(!isset($contributor)){
            return response()->json([
                "message" => "Contributor not found."
            ]);
        }

        if (isset($request->link)) {
            $collection = Collection::where('link', $request->link)->first();
            //Перевірка на співпадіння за посиланням
            if(!isset($collection)){
                return response()->json([
                    "message" => "Collection link is incorrect!"
                ]);
            }
            $contributor->collection_id = $collection->id;
        }

        if (isset($request->user_name)){
            $contributor->user_name = $request->user_name;
        }

        if (isset($request->amount)) {
            $contributor->amount = $request->amount;
        }

        $contributor->save();

        return response()->json([
            "message" => "Contributor has been updated successfully!"
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteContributor($id){
        $contributor = Contributor::where('id', $id)->first();

        //Перевірка на співпадіння за ідентифікатором
        if(!isset($contributor)){
            return response()->json([
                "message" => "Contributor not found."
            ]);
        }

        $contributor->delete();

        return response()->json([
            "message" => "Contributor has been deleted."
        ]);
    }
}
