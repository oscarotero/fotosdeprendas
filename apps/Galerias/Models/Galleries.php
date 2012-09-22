<?php
namespace Apps\Galerias\Models;

class Galleries {
	private $path;

	public function __construct ($path) {
		$this->path = $path;
	}

	public function get () {
		$galleries = glob($this->path.'*', GLOB_ONLYDIR);
		$limit = strlen($this->path);

		foreach ($galleries as &$gallery) {
			$gallery = substr($gallery, $limit);
		}

		return $galleries;
	}

	public function exists ($gallery) {
		return is_dir($this->path.$gallery);
	}

	public function create ($gallery) {
		if (!$this->exists($gallery)) {
			mkdir($this->path.$gallery, 0777);
		}
	}

	public function getPhotos ($gallery) {
		$photos = glob($this->path.$gallery.'/*.jpg');
		$limit = strlen($this->path.$gallery.'/');

		$list = array();

		foreach ($photos as $photo) {
			$list[] = array(
				'file' => substr($photo, $limit)
			);
		}

		return $list;
	}

	public function uploadPhoto ($gallery, $photo) {
		if ($this->exists($gallery)) {
			$file = $this->path.$gallery.'/'.strtolower($photo['name']);

			if ($photo['error'] !== 0 || rename($photo['tmp_name'], $file) === false) {
				return false;
			}

			chmod($file, 0755);

			$Imagecow = \Imagecow\Image::create();
			$Imagecow->load($file)->resize(1200, 1200)->save();

			return true;
		}

		return false;
	}

	public function rotatePhoto ($gallery, $photo) {
		if ($this->exists($gallery)) {
			$file = $this->path.$gallery.'/'.$photo;

			if (is_file($file)) {
				$Imagecow = \Imagecow\Image::create();
				$Imagecow->load($file)->rotate(-90)->save();

				return true;
			}
		}

		return false;
	}
}