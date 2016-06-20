<?php
namespace App\Model;

use Nette;

class Papers extends Nette\Object {

	const DOCUMENT_LINK = 'http://www.mvcr.cz/soubor/prehled-k-23-5-2016.aspx';
	const FILE_PATH = '/tmp';
	const FILE_NAME = 'docchecker.xls';
	const INPUT_FILE_NAME = self::FILE_PATH.'/'.self::FILE_NAME;

	const ALL_SHEET = 'all';
	const ALL_YEARS = 'all';
	const LONGTERM_SHEET = 'DP, PP, DV - prodl.';
	const EMPLOYEECARD_SHEET = 'Zaměstnanecká karta';
	const PERMANENT_SHEET = 'Trvalé pobyty';

	const LONGTERM_OPT = 'lt';
	const EMPLOYEECARD_OPT = 'ec';
	const PERMANENT_OPT = 'pt';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	public function getByNumber($number, $sheetname = null, $year = null) {
		$data = $this->database->table('papers')
			->select('*')
			->where('papers.number', $number)
			->where('papers.deleted IS NULL');
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

	// public function check($paperNumber, $rawSheetname = self::ALL_SHEET, $year = self::ALL_YEARS) {
	// 	$this->_getLatestXlsFileFromMvcr();
	// 	$objReader = \PHPExcel_IOFactory::createReader('Excel5');
	// 	// handle specific sheetname
	// 	if ($rawSheetname != self::ALL_SHEET) {
	// 		$sheetname = $this->_getOriginalSheetname($rawSheetname);
	// 		$objReader->setLoadSheetsOnly($sheetname);
	// 	}
	// 	$objPHPExcel = $objReader->load(self::INPUT_FILE_NAME);
	// 	$data = ['date', 'numbers' => []];
	// 	// make search in all or specefic sheet by checking each cell value
	// 	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
	// 		$data['date'] = $worksheet->getCell('B5')->getValue() 
	// 				? $worksheet->getCell('B5')->getValue() 
	// 				: 'No data';
	// 		foreach ($worksheet->getRowIterator() as $row) {
	// 			$cellIterator = $row->getCellIterator();
	// 			foreach ($cellIterator as $cell) {
	// 				if (!is_null($cell->getValue())) {
	// 					$condition = '/(?=.*OAM-'.$paperNumber.')/';
	// 					if ($year != self::ALL_YEARS) {
	// 						$condition = '/(?=.*OAM-'.$paperNumber.')(?=.*-'.$year.')/';
	// 					}
	// 					if (preg_match($condition, $cell->getValue())) {
	// 						preg_match('/[A-Z-0-9\/]+/', $cell->getValue(), $matches);
	// 						$data['numbers'][$worksheet->getTitle()][] = $matches[0];
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	return $data;
	// }

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

	protected function _getOriginalSheetname($rawSheetname) {
		switch ($rawSheetname) {
			case self::LONGTERM_OPT:
				return self::LONGTERM_SHEET;
			case self::EMPLOYEECARD_OPT:
				return self::EMPLOYEECARD_SHEET;
			case self::PERMANENT_OPT:
				return self::PERMANENT_SHEET;
			default:
				return self::ALL_SHEET;
		}
	}

	protected function _getLatestXlsFileFromMvcr() {
		if ($this->_getCurrentFileSize() != $this->_getRemoteFileSize()) {
			return file_put_contents(self::FILE_PATH.'/'.self::FILE_NAME, fopen(self::DOCUMENT_LINK, 'r'));
		}
		return false;
	}

	protected function _getCurrentFileSize() {
		if (file_exists(self::FILE_PATH.'/'.self::FILE_NAME)) {
			return filesize(self::FILE_PATH.'/'.self::FILE_NAME);
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
