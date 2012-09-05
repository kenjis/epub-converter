<?php

namespace Fuel\Tasks;

/**
 * Epub Task
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    AGPL 3.0 http://opensource.org/licenses/AGPL-3.0
 */

class Epub
{
	/**
	 * php oil r epub
	 */
	public static function run()
	{
		static::help();
	}
	
	/**
	 * php oil r epub:help
	 */
	public static function help()
	{
		echo <<<EOL
Usage:
  oil refine epub:create_kepub  ... convert epub files to  kepub files

EOL;
	}
	
	/**
	 * php oil r epub:create_kepub
	 */
	public static function create_kepub()
	{
		$filelist = \File::read_dir(DOCROOT . '/files/epub');
		//var_dump($filelist);
		
		foreach ($filelist as $file)
		{
			$info = pathinfo($file);
			//var_dump($info);
			
			if ($info['extension'] === 'epub')
			{
				$epub = new \Model_Epub();
				$epub->set_epub_dir(DOCROOT . '/files/epub');
				$epub->set_kepub_dir(DOCROOT . '/files/kepub');
				$epub->set_filename($file);
				$epub->set_prefix(date('Ymd_'));
				echo 'Building... ' . $epub->get_kepub_filename() . PHP_EOL;
				$epub->build_kepub();
			}
		}
	}
}

/* End of file tasks/epub.php */