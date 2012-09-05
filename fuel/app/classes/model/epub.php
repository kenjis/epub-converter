<?php

/**
 * Epub Model
 *
 * @author     Kenji Suzuki https://github.com/kenjis
 * @copyright  2012 Kenji Suzuki
 * @license    AGPL 3.0 http://opensource.org/licenses/AGPL-3.0
 */
class Model_Epub
{
	protected $filename;
	protected $prefix;  // prefix to kepub filename
	protected $epub_dir;
	protected $kepub_dir;
	protected $work_dir;
	protected $file_list;
	protected $rootfile;
	protected $html_filelist;
	
	public function set_filename($filename)
	{
		$this->filename = $filename;
	}
	
	public function set_epub_dir($dir)
	{
		$this->epub_dir = $dir;
	}
	
	public function set_kepub_dir($dir)
	{
		$this->kepub_dir = $dir;
	}
	
	public function set_prefix($prefix)
	{
		$this->prefix = $prefix;
	}
	
	public function extract()
	{
		$tmp_dir = APPPATH . 'tmp/epub';
		if ( ! file_exists($tmp_dir))
		{
			mkdir($tmp_dir);
		}
		
		$file = $this->epub_dir . '/' . $this->filename;
		$this->work_dir = $tmp_dir . '/' . getmypid();
		
		if (file_exists($this->work_dir))
		{
			File::delete_dir($this->work_dir, true, false);
		}
		else
		{
			mkdir($this->work_dir);
		}
		
		$unzip = new Unzip();
		$this->file_list = $unzip->extract($file, $this->work_dir);
		
		if (count($this->file_list) > 5)
		{
			return true;
		}
		else
		{
			throw new FuelException('EPUB file is damaged?');
		}
	}
	
	public function get_rootfile()
	{
		if (is_null($this->rootfile))
		{
			$this->read_opf();
		}
		
		return $this->rootfile;
	}
	
	protected function read_opf()
	{
		if (is_null($this->work_dir))
		{
			$this->extract();
		}
		
		$file = $this->work_dir . '/' . 'META-INF/container.xml';
		$xml = simplexml_load_file($file);
		$this->rootfile = (string) $xml->rootfiles->rootfile->attributes()->{'full-path'};
	}
	
	public function get_html_filelist()
	{
		if (is_null($this->html_filelist))
		{
			$this->read_rootfile();
		}
	
		return $this->html_filelist;
	}
	
	protected function read_rootfile()
	{
		if (is_null($this->rootfile))
		{
			$this->get_rootfile();
		}
		
		$file = $this->work_dir . '/' . $this->rootfile;
		$xml = simplexml_load_file($file);
		//var_dump($xml->manifest); exit;
		
		$this->html_filelist = array();
		
		foreach ($xml->manifest->item as $item) {
			//var_dump($item->attributes()->{'media-type'});
			
			if ((string) $item->attributes()->{'media-type'} === 'application/xhtml+xml')
			{
				$this->html_filelist[] = (string) $item->attributes()->href;
			}
		}
		
		//var_export($this->html_filelist); exit;
	}
	
	public function build_kepub()
	{
		if (is_null($this->html_filelist))
		{
			$this->get_html_filelist();
		}
		
		$dir = dirname($this->rootfile);
		
		foreach ($this->html_filelist as $index => $file)
		{
			$file = $dir . '/' . $file;
			
			// for debug
			copy($this->work_dir . '/' . $file, $this->work_dir. '/' . $file . '.orig');
			
			$this->add_kepub_span($file);
		}
		
		$this->create_zip();
	}
	
	protected function create_zip()
	{
		// create Zip file
		$zip = new ZipArchive();
		$filename = $this->kepub_dir . '/' . $this->get_kepub_filename();
		
		if ($zip->open($filename, ZIPARCHIVE::CREATE) !== true)
		{
			exit("cannot open <$filename>\n");
		}
		
		foreach ($this->file_list as $file_to_add)
		{
			$len = strlen($this->work_dir);
			$localname = substr($file_to_add, $len + 1);
			//var_dump($localname); exit;
			$zip->addFile($file_to_add, $localname);
		}
		
		$zip->close();
	}
	
	protected function add_kepub_span($file)
	{
		$file = $this->work_dir . '/' . $file;
		$lines = file($file);
		//var_dump($lines); exit;
		
		$content = '';
		
		foreach ($lines as $index => $line)
		{
			//echo $line;
			
			$para = $index + 1;
			
			if (preg_match('|(.*)<p>(.*)</p>(.*)|u', $line, $matches))
			{
				//var_dump($matches);
				$sentense = 1;
				
				$new = $matches[1] . '<p><span id="kobo.' . $para . '.' . $sentense .'">'
					. $matches[2] . '</span></p>' . $matches[3] . "\n";
				
				//echo "\n", $line;
				//echo $new;
				
				$content .= $new;
			}
			else
			{
				$content .= $line;
			}
		}
		
		file_put_contents($file, $content);
	}
	
	public function get_kepub_filename()
	{
		$info = pathinfo($this->filename);
		//var_dump($info);
		return $this->prefix . $info['filename'] . '.kepub.epub';
	}
}
