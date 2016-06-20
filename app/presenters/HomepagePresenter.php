<?php

namespace App\Presenters;

use Nette;
use App\Model\Papers;
use Form;

class HomepagePresenter extends BasePresenter {

	/** @var Form\ISearchFormFactory @inject */
	public $searchFormFactory;

	/** @var Nette\Database\Context */
	private $database;

	/** @var papersModel */
	private $papersModel;

	public function __construct(Nette\Database\Context $database, Papers $papersModel) {
		$this->database = $database;
		$this->papersModel = $papersModel;
	}

	public function renderDefault() {
		$this->template->results = $searchValue = null;
		if (isset($this['searchForm']->components['searchForm'])) {
			$searchValue = $this['searchForm']->components['searchForm']->httpData['number'];
			$this->template->results = $this->papersModel->getByNumber($searchValue);
		}
	}

	protected function createComponentSearchForm() {
		return $this->searchFormFactory->create();
	}

}
