<?php
/**
 * Search and store new photos.
 *
 * @package    api
 * @subpackage admin
 */
class MT_Admin_Model_PhotoSearch {

	/**
	 * Timestamp
	 * 
	 * @var int
	 */
	private $time;
	
	/**
	 *
	 * @var array
	 */
	private $galleriesFullPath;
	
	public function __construct() {
		// Save galleries paths into array
		$query = ORM::for_table(DB_PREFIX.'gallery')
			->select_many([
				'id',
				'fullPath'
			]);
		foreach ($query->find_many() as $item) {
			$this->galleriesFullPath[$item->id] = $item->fullPath.'/';
		}

		return $this;
	}
	
	/**
	 * Searchs new photos in the given directory and stores them.
	 *
	 * @param	string|null $dir Directory
	 * @return	boolean True, if search was successful
	 */
	public function search($dir = MT_Admin_Model_File::PHOTO_PATH) { 
		if (!is_dir($dir)) {
			return FALSE;
		}
		$directoryHandle = opendir( $dir );
		while(false !== ($basename = readdir($directoryHandle))) {
			$path = $dir.'/'.$basename;
			
			// Skip "." and ".." files and the thumbnail folder
			if($basename == '.' || $basename == '..' || $path == MT_Admin_Model_File::THUMBNAIL_PATH) {
				continue;
			}
			// Folder	
			else if(is_dir($path)) {
				$this->search($path);
			}
			// Photo file
			else if(MT_Admin_Model_File::isPhoto($path)) {
				// Store the photo path without PHOTO_PATH in the database
				$dbDirname = MT_Admin_Model_File::getDbPathFromDir($dir);
				$dbFile = $dbDirname.$basename;
		
				if (!isset($this->time)) {
					$this->time = time();
				} else {
					$this->time += MT_Admin_View_PhotoEdit::SECONDS_BETWEEN_PHOTOS;
				}

				// Ueberpruefen ob das Bild bereits in der Datenbank gespeichert ist
				if(!$this->checkPhotoIsInDb($dbFile)) {
					MT_Photo::insert(array(
						'path'        => $dbFile,
						'name_old'    => $basename,
						'gallery'     => $this->getGalleryIdFromPath($dbDirname),
						'date'        => $this->time,
						'show'        => 0
					));
				}	
			}
		}
		closedir($directoryHandle);
		return TRUE;
	}
	
	/**
	 * Check photo is in database
	 *
	 * @param string $path Photo's database path
	 * @return boolean
	 */	
	private function checkPhotoIsInDb($path) {
		$item = ORM::for_table(DB_PREFIX.'photo')
			->where_equal('path', $path)
			->find_one();
		if ($item) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * Gibt die ID der Galerie zurück, wenn man den ganzen Pfad von dieser
	 * angibt
	 *
	 * @param string $path Galleries full path
	 * @return int|bool False oder ID
	 */
	private function getGalleryIdFromPath($path) {
		// Add a backslash, if path doesn't end with one
		if(substr($path, -1) != '/') {
			$path .= '/';
		}
		return array_search($path, $this->galleriesFullPath);	
	}

}