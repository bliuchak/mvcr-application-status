<?php
namespace App\Model;

use Nette;

class Papers extends Nette\Object {

	const DOCUMENT_LINK = 'http://www.mvcr.cz/soubor/prehled-k-23-5-2016.aspx';
	const INPUT_FILE_NAME = '/tmp/docchecker.xls';

	/** @inject @var \Nette\Database\Context */
	public $database;

	/** @inject @var \Lib\Settings */
	public $settings;

	public function __construct(\Nette\Database\Context $database, \Lib\Settings $settings) {
		$this->database = $database;
		$this->settings = $settings;
	}

	public function getByNumber($number, $type = null, $year = null) {
		$data = $this->database->table('papers')
			->select('*')
			->where('papers.number', $number)
			->where('papers.deleted IS NULL');
		// type filter
		if (in_array($type, $this->settings->params['types'])) {
			$data->where('papers.type', $type);
		}
		// year filter
		if (in_array($year, $this->settings->params['years'])) {
			$data->where('papers.year', $year);
		}
		return $data->fetchAll();
	}

	public function updateDatabase() {
		// mark all prev data as outdated
		$this->database->query('UPDATE papers SET deleted=?', time());
		$this->_getLatestXlsFileFromMvcr();
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load(self::INPUT_FILE_NAME);
		$data = [];
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			foreach ($worksheet->getRowIterator() as $row) {
				$cellIterator = $row->getCellIterator();
				foreach ($cellIterator as $cell) {
					if (!is_null($cell->getValue())) {
						$condition = '/(?=.*OAM-)/';
						if (preg_match($condition, $cell->getValue())) {
							preg_match('/[A-Z-0-9\/]+/', $cell->getValue(), $matches);
							$number = $this->_getNumberDetailsByRawNumber($matches[0]);
							$data[] = [
								'rawNumber' => $number['rawNumber'],
								'number' => $number['number'],
								'type' => $number['type'],
								'year' => $number['year'],
								'created' => time()
							];
						}
					}
				}
			}
		}
		if (count($data)) {
			$this->database->table('papers')->insert($data);
		}
		return $data;
	}

	protected function _getNumberDetailsByRawNumber($rawNumber) {
		$dividedNumber = explode('/', $rawNumber);
		$number = explode('-', $dividedNumber[0]);
		$typeYear = explode('-', $dividedNumber[1]);
		$data = [
			'rawNumber' => $rawNumber,
			'number' => $number[1],
			'type' => $typeYear[0],
			'year' => $typeYear[1]
		];
		return $data;
	}

	protected function _getLatestXlsFileFromMvcr() {
		if ($this->_getCurrentFileSize() != $this->_getRemoteFileSize()) {
			if (file_exists(self::INPUT_FILE_NAME)) {
				unlink(self::INPUT_FILE_NAME);
			}
			return file_put_contents(self::INPUT_FILE_NAME, fopen(self::DOCUMENT_LINK, 'r'));
		}
		return false;
	}

	protected function _getCurrentFileSize() {
		if (file_exists(self::INPUT_FILE_NAME)) {
			return filesize(self::INPUT_FILE_NAME);
		}
		return 0;
	}

	protected function _getRemoteFileSize() {
		$size = 0;
		$fileHeaders = $this->_getRemoteFileHeaders();
		if (preg_match('/Content-Length: (\d+)/', $fileHeaders, $matches)) {
			$size = isset($matches[1]) ? $matches[1] : 0;
		}
		return (int) $size;
	}

	protected function _getRemoteFileHeaders() {
		$file = self::DOCUMENT_LINK;

		$ch = curl_init($file);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

}
