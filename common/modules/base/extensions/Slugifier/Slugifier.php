<?php
namespace common\modules\base\extensions\Slugifier;

use common\modules\base\components\Debug;
use common\modules\base\extensions\Slugifier\Exception\TransliterateException;

class Slugifier
{
	/**
	 * @var string
	 */
	public $string;
	/**
	 * @link http://userguide.icu-project.org/transforms/general
	 *
	 * @var string
	 */
	public $transliterateOptions;
	/**
	 * @var string
	 */
	public $replacement = '-';
	/**
	 * @var bool
	 */
	public $lowercase = true;

	/**
	 * @param $string
	 */
	public function __construct($string) {
		$this->string = $string;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slugify($this->string);
	}

	/**
	 * @param $string
	 *
	 * @return string
	 *
	 * @throws TransliterateException
	 */
	private function slugify($string) {
		$string = $this->transliterate($string);
		$string = preg_replace('/[^\/\\\a-zA-Z0-9=\s—–-]+/u', '', $string);
		$string = preg_replace('/[\/\\\=\s—–-]+/u', $this->replacement, $string);
		$string = trim($string, $this->replacement);

		return $this->lowercase ? mb_strtolower($string) : $string;
	}

	/**
	 * @param $string
	 *
	 * @return string
	 *
	 * @throws TransliterateException
	 */
	private function transliterate($string) {
		if (!$this->transliterateOptions) {
			$this->transliterateOptions = 'Any-Latin;Latin-ASCII;[:Nonspacing Mark:]Remove;NFKC;[:Punctuation:]Remove;';
		}
		
		$transliterator = \Transliterator::create($this->transliterateOptions);
		if (!$transliterator instanceof \Transliterator) {
			throw new TransliterateException('$transliterator is not instance of Transliterator');
		}

		$string = $transliterator->transliterate($string);
		if ($string === false) {
			throw new TransliterateException($transliterator->getErrorMessage(), $transliterator->getErrorCode());
		}

		return $string;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getSlug();
	}
}