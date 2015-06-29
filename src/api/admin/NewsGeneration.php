<?php
/**
 * Automatically generate news entries based on new photo uploads (and newly
 * create galleries).
 * 
 * @package api
 * @subpackage admin
 */
class MT_Admin_NewsGeneration extends MT_Admin_Common {

	/**
	 * Timestamp of the last news
	 * 
	 * @var integer 
	 */
	private $timestampLatestNews;

	/**
	 * 
	 * @return int HTTP status code
	 */
	public function action() {
		$this->timestampLatestNews = parent::getAggregate('news', 'MAX', 'date');
		$timestampLatestPhoto = parent::getAggregate('photo', 'MAX', 'date');
		
		if ($this->timestampLatestNews < $timestampLatestPhoto) {
			return parent::getList($this->getGeneratedNews());
		} else {
			return parent::getList(array());
		}
	}
	
	/**
	 * 
	 * @return array
	 */
	private function getGeneratedNews() {
		$query = ORM::for_table('wp_mt_photo')
					->select_many(array(
						'galleryName' => 'wp_mt_gallery.name',
						'categoryName' => 'wp_mt_category.name',
						'subcategoryName' => 'wp_mt_subcategory.name'
					))
					->select_expr('COUNT(wp_mt_gallery.id)', 'numPhotos')
					->inner_join('wp_mt_gallery', 'wp_mt_gallery.id = wp_mt_photo.gallery')
					->inner_join('wp_mt_category', 'wp_mt_category.id = wp_mt_gallery.category')
					->left_outer_join('wp_mt_subcategory', 'wp_mt_subcategory.id = wp_mt_gallery.subcategory')
					->where_equal('wp_mt_photo.show', 1)
					->where_gte('wp_mt_photo.date', $this->timestampLatestNews)
					->group_by(array('wp_mt_category.name', 'wp_mt_subcategory.name', 'wp_mt_gallery.name'))
					->order_by_desc('numPhotos');

		$news = array();
		foreach ($query->find_many() as $item) {
			array_push($news, array(
				'title' => self::generateTitle($item->categoryName, $item->subcategoryName, $item->galleryName, $item->date, $item->numPhotos),
				'text' => self::generateText($item->numPhotos),
				'gallery' => $item->id
			));
		}
		return $news;
	}
	
	/**
	 * Generates the title of the news entry.
	 * 
	 * @param string $catgegoryName Name of the category
	 * @param string|null $subcategoryName Name of the subcategory
	 * @param string $galleryName Name of the gallery
	 * @param int $galleryDate Date of the gallery as timestamp
	 * @param int $numPhotos Number of added photos
	 * @return string Title of the news
	 */
	private function generateTitle($catgegoryName, $subcategoryName = NULL, $galleryName, $galleryDate, $numPhotos) {
		$title = $catgegoryName;
		if( !empty($subcategoryName) ) {
			$title .= ' > ' . $subcategoryName;
		}
		$title .= ': ';
		// New gallery
		if($galleryDate  >= $this->timestampLatestNews) {		
			$title .= "Neue Galerie '" . $galleryName . "'";
		}
		// New photos only
		else {
			if($numPhotos != 1) {
				$title .= 'Neue Bilder';
			} else {
				$title .= 'Neues Bild';
			}
			$title .= " in der Galerie '" . $galleryName . "'";
		}
		return $title;
	}
	
	/**
	 * Generates the text of the news entry.
	 * 
	 * @param int $numPhotos Number of photos added
	 * @return string Text of the news
	 */
	private function generateText($numPhotos) {
		$text = $numPhotos . ' ';
		if($numPhotos > 1) {
			$text .= 'neue Bilder';
		} else {
			$text .= 'neues Bild';
		}
		return $text;
	}
}
