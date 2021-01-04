<?php
namespace App\Helpers;

class ZoomApiHelper{

	public static function createZoomMeeting($meetingConfig = []){
		$jwtToken = config('zoom.ZOOM_API_TOKEN');
		
		$requestBody = [
			'topic'			=> $meetingConfig['topic'] 		?? 'PHP General Talk',
			'type'			=> $meetingConfig['type'] 		?? 2,
			'start_time'	=> $meetingConfig['start_time']	?? date('Y-m-dTh:i:00').'Z',
			'duration'		=> $meetingConfig['duration'] 	?? 30,
			'password'		=> $meetingConfig['password'] 	?? mt_rand(),
			'timezone'		=> 'Africa/Cairo',
			'agenda'		=> 'PHP Session',
			'settings'		=> [
			  	'host_video'			=> false,
			  	'participant_video'		=> true,
			  	'cn_meeting'			=> false,
			  	'in_meeting'			=> false,
			  	'join_before_host'		=> true,
			  	'mute_upon_entry'		=> true,
			  	'watermark'				=> false,
			  	'use_pmi'				=> false,
			  	'approval_type'			=> 1,
			  	'registration_type'		=> 1,
			  	'audio'					=> 'voip',
				'auto_recording'		=> 'none',
				'waiting_room'			=> false
			]
		];

		$zoomUserId = config('settings.ZOOM_API_USER_ID');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.zoom.us/v2/users/".$zoomUserId."/meetings",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($requestBody),
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".$jwtToken,
		    "Content-Type: application/json",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return [
		  	'success' 	=> false, 
		  	'msg' 		=> 'cURL Error #:' . $err,
		  	'response' 	=> ''
		  ];
		} else {
		  return [
		  	'success' 	=> true,
		  	'msg' 		=> 'success',
		  	'response' 	=> json_decode($response)
		  ];
		}
	}
}