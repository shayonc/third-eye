<?php


namespace App\Http\Controllers;


use App\Device;
use Carbon\Carbon;

class ApiController {

	public function index() {

		// sms format: 0:device_id, 1:loc_x, 2:loc_y, 3:timestamp, 4:amb_temp, 5:humidity, 6:body_temp

		$data = request()->input();

		if ( isset( $data[ 'Body' ] )  ) {

			$in_data = explode( ',', $data['Body'] );
			if(isset($in_data)) {

				$child = Device::find( $in_data['0'] )->child()->first();

				$child->locations()->create( [
					'timestamp' => Carbon::now(), //$in_data['0'],
					'geolat'    => $in_data['1'],
					'geolng'    => $in_data['2'],
					// 'geoele'    => $in_data['4'],
				] );

				$child->health_data()->create( [
					'timestamp'           => Carbon::now(), //$in_data['0'],
					'body_temperature'    => $in_data['6'],
					'ambient_temperature' => $in_data['4'],
					'humidity'            => $in_data['5'],
				] );

			} else {
				return response()->json([
					'Status' => 'FAIL',
					'Message' => 'Message format wrong'
				]);
			}

		} else {

			return response()->json([
				'Status' => 'FAIL',
				'Message' => 'Request does not include body'
			]);

		}

	}
}