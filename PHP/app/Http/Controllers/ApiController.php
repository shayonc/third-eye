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
			$filename = $file->getFilename().'.'.$extension,  File::get($file);
			Storage::disk('local')->put($filename);

			return response()->json([
				'Status' => 'OK',
				'Message' => Storage::disk('local')->url($filename),
				'Words' => $this->getVideoData(Storage::disk('local')->url($filename)
			]);
		} else {
			return response()->json([
				'Status' => 'No File found',
				'Message' => Storage::disk('local')->url($file->getFilename().'.'.$extension,  File::get($file))
			]);
		}

	}

	public function getVideoData( $videoFile ){

		//get from google
		$words = array("Person", "Buidling", "Thing");
		return $words;

	}

}