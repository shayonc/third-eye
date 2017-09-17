<?php
namespace App\Http\Controllers;

use CURLFile;
use IBMWatson\VisualRecognition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Google\Cloud\VideoIntelligence\V1beta1\VideoIntelligenceServiceClient;
use Google\Cloud\Videointelligence\V1beta1\Feature;
use Google\Cloud\Storage\StorageClient;

class ApiController {

	public $file;
	public $extension;
	public $filename;
	public $prefix;
	Public $filePath;


	public function index() {

		$file = request()->file('filefield');
		$this->extension = $file->getClientOriginalExtension();
		$this->filename = $file->getFilename().'.'.$this->extension;
		$this->prefix = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
		$this->filePath = $this->prefix . $this->filename;

		if(isset($file) && !in_array($this->extension, ["jpg", "jpeg", "png"])){

			Storage::disk('local')->put($this->filename, File::get($file));

			return response()->json([
				'Status' => 'OK',
				'Message' => "Video Processed",
				'File' =>  $this->filename,
				'this-Words' => $this->getVideoData( $this->prefix . $this->filename, $this->filename )
			]);

		} else if(isset($file) && in_array($this->extension, ["jpg", "jpeg", "png"])) {

			Storage::disk('local')->put($this->filename, File::get($file));
			$this->prefix = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

			return response()->json([
				'Status' => 'OK',
				'Message' => "Image Processed",
				'File' =>  $this->filename,
				'Words' => $this->getImageData( $this->prefix . $this->filename, $this->filename )
			]);
		}else{
			return response()->json([
				'Status' => 'No File found'
			]);
		}

	}

	public function getImageData(){

		$key = "8d0c1a9339042d5c158149d67a4f070d5f488bc6";


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, true);
		$data = array("images_file" => new CURLFile($this->filePath,mime_content_type($this->filePath),basename($this->filePath)));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_URL, "https://gateway-a.watsonplatform.net/visual-recognition/api/v3/classify?version=2015-12-02&api_key=".$key);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		$result = json_decode($response, true);

		$words = array();
		$classifiers = $result["images"][0]["classifiers"][0];

		foreach ( $classifiers["classes"] as $word) {
			$words[] = $word["class"];
		}

		return $words;

	}

	public function getVideoData( $videoFile, $filename ){

		$uri = $this->saveVideoToCloud($videoFile, $this->filename);
		$features = [ 1 ];
		$key = "AIzaSyCAxBazE0UbhS9_bo7Afa3DB6t8x94JtMw";

		// putenv("GOOGLE_APPLICATION_CREDENTIALS=/Users/jyoansah/Dev/ThirdEye/config/key.json");

		$client = new \Google_Client();
		$client->useApplicationDefaultCredentials();


		error_reporting(9);
		$this->filePath = $this->prefix . $this->filename;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, true);
		$data = array("inputUri" => ($uri),
		              "features" => $features );
		// dd($data);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_URL, "https://videointelligence.googleapis.com/v1beta1/videos:annotate?key=".$key);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen(json_encode($data)),
				'Bearer: AIzaSyCAxBazE0UbhS9_bo7Afa3DB6t8x94JtMw' )
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		$result = json_decode($response, true);

		dd($response);

		$words = array();

		// //get from google
		// $video = new VideoIntelligenceServiceClient();

		// # Read the local video file and convert it to base64
		// $inputContent = base64_encode(
		// 	file_get_contents("$videoFile")
		// );

		// # Execute a request.
		// $operation = $video->annotateVideo(
		// 	$uri,
		// 	[Feature::LABEL_DETECTION]
		// );

		// // dd($operation);

		// # Wait for the request to complete.
		// $operation->pollUntilComplete();

		// // dd($operation);

		// $words = array();
		// # Print the result.
		// if ($operation->operationSucceeded()) {
		// 	// dd($operation->getResult());
		// 	$results = $operation->getResult()->getAnnotationResults()[0];
		// 	foreach ($results->getLabelAnnotations() as $label) {
		// 		$words[] = ($label->getDescription() . PHP_EOL);
		// 		// foreach ($label->getLocations() as $location) {
		// 		// 	printf('  %ss to %ss' . PHP_EOL,
		// 		// 		$location->getSegment()->getStartTimeOffset() / 1000000,
		// 		// 		$location->getSegment()->getEndTimeOffset() / 1000000);
		// 		// }
		// 	}
		// } else {
		// 	print_r($operation->getError());
		// }


		return $words;

	}

	public function saveVideoToCloud($video, $filename){
		# Your Google Cloud Platform project ID
		$projectId = ['thirdeye-x'];

		# The name for the new bucket
		$bucketName = 'thirdeye-x.appspot.com';

		# Instantiates a client
		$storage = new StorageClient([
			'projectId' => $projectId
		]);

		$bucket = $storage->bucket($bucketName);

		$options = [
			'resumable' => true,
			'name' => $this->filename,
			'metadata' => [
				'contentLanguage' => 'en'
			]
		];

		$object = $bucket->upload(
			fopen($video, 'r'),
			$options
		);

		// dd($object->info()["bucket"]);
		return  "gs://" . $object->info()["bucket"] . '/' . $object->info()["name"];

	}


}