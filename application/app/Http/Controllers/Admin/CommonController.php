<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class CommonController extends Controller
{
	public function deletedata(Request $req)
	{
		$id = $req->id;
		$deletetype = $req->deletetype;
		$tablename = $req->tablename;

		$timestamp = Carbon::now();

		if ($deletetype == 'single') {
			DB::table($tablename)->where('id', $id)->update(['deleted_at' => $timestamp]);
		} elseif ($deletetype == 'multiple') {
			DB::table($tablename)->whereIn('id', $id)->update(['deleted_at' => $timestamp]);
		}

		return response()->json(['deleted_id' => $id]);
	}

	public function permissions(Request $req)
	{
		$approve_val = $req->approve_val;
		$valuearray = explode(',', $approve_val);
		$tablename = $req->tablename;
		$status = $req->status;
		
		if(isset($req->statuscol) && $req->statuscol != 'undefined'){
			$statuscol = $req->statuscol;
			$arrayuval =  array(
				$statuscol => $status
			);
		}
		else{
			$arrayuval =  array(
				'status' => $status
			);
		}
		
		$updval = DB::table($tablename)->whereIn('id', $valuearray)->update($arrayuval);

		return Redirect::back();
	}

	public function updateSlug(Request $req)
	{
		//////////////// Update Slug /////////////////
		$getDatas = DB::table('menus')->get();
		foreach ($getDatas as $gData) {

			$menuUpdate = array(
				'uri' => Str::slug($gData->title),
				'updated_at' => date('Y-m-d H:i:s')
			);

			DB::table('menus')->where('id', $gData->id)->update($menuUpdate);
		}
	}

	public function sampleFileDownload(Request $request)
	{
		$getFile = $request->filename . '.csv';
		$filePath = public_path("samplefiles/" . $getFile);
		$headers = ['Content-Type: application/csv'];
		return response()->download($filePath, $getFile, $headers);
	}
}
