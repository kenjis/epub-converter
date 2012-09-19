<?php

/**
 * Upload Controller
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    AGPL 3.0 http://opensource.org/licenses/AGPL-3.0
 */
class Controller_Upload extends Controller
{
	public function get_index()
	{
		return Response::forge(View::forge('upload/index'));
	}
	
	public function post_index()
	{
		Upload::process();
		
		if (Upload::is_valid())
		{
			Upload::save();
		}
		
		foreach (Upload::get_errors() as $file)
		{
			//Debug::dump($file);
			
			$error = $file['errors']['0']['message'];
			
			$view = View::forge('upload/index');
			$view->set('error', $error);
			
			return Response::forge($view);
		}
		
		$results = Upload::get_files();
		$file = $results[0]['saved_as'];
		
		$epub = $this->create_kepub($file);
		
		// remove uploaded epub file
		unlink($results[0]['saved_to'] . $file);
		
		$epub->download();  // exit
	}
	
	protected function create_kepub($file)
	{
		! ini_get('safe_mode') and set_time_limit(0);
		
		$kepub_dir = APPPATH . 'tmp/kepub/' . getmypid();
		
		$epub = new \Model_Epub();
		$epub->set_epub_dir(APPPATH . 'tmp');
		$epub->set_kepub_dir($kepub_dir);
		$epub->set_filename($file);
		$epub->set_prefix(date('Ymd_'));
		$epub->set_image_max_size(600, 800);
		$epub->build_kepub();
		
		return $epub;
	}
}
