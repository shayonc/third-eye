<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ApiController {

	public function index() {

		$file = Request::file('filefield');

		if(isset($file)){

			$extension = $file->getClientOriginalExtension();
			Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));

			return response()->json([
				'Status' => 'OK',
				'Message' => Storage::disk('local')->url($file->getFilename().'.'.$extension,  File::get($file)),
				'Words' => $this->getVideoData()
			]);
		} else {
			return response()->json([
				'Status' => 'No File found',
				'Message' => Storage::disk('local')->url($file->getFilename().'.'.$extension,  File::get($file))
			]);
		}

	}

	public function getVideoData(){

		//get from google
		$words = array("Person", "Buidling", "Thing");
		return $words;

	}

}