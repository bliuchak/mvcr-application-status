<?php

namespace App\Presenters;

use Nette;
use App\Model\Papers;
use Form;

class HomepagePresenter extends BasePresenter {

	/** @var Form\ISearchFormFactory @inject */
	public $searchFormFactory;

	/** @var \App\Model\Papers @inject */
	public $papersModel;

	public function renderDefault() {
		$this->template->results = $searchValue = null;
		if (isset($this['searchForm']->components['searchForm'])) {
			$searchValue = $this['searchForm']->components['searchForm']->httpData['number'];
			$type = $this['searchForm']->components['searchForm']->httpData['type'];
			$year = $this['searchForm']->components['searchForm']->httpData['year'];
			$this->template->results = $this->papersModel->getByNumber($searchValue, $type, $year);
		}
	}

	protected function createComponentSearchForm() {
		return $this->searchFormFactory->create();
	}

}
