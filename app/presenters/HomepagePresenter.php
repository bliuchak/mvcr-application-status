<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Form;

class HomepagePresenter extends BasePresenter {

	/** @var Form\ISearchFormFactory @inject */
	public $searchFormFactory;

	public function renderDefault() {
		$this->template->results = $searchValue = null;
		if (isset($this['searchForm']->components['searchForm'])) {
			$papersModel = new \App\Model\Papers();
			$searchValue = $this['searchForm']->components['searchForm']->httpData['number'];
			$this->template->results = $papersModel->check($searchValue);
		}
	}

	protected function createComponentSearchForm() {
		$control = $this->searchFormFactory->create();
		return $control;
	}

}
