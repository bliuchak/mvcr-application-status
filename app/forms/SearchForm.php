<?php

namespace Form;

use Nette;
use Nette\Application\UI;
use App\Model;

class SearchForm extends UI\Control {

	/** @var \Model\Papers */
	private $papersModel;

	/** @var \Lib\Settings */
	private $settings;

	public function __construct(Model\Papers $papers, \Lib\Settings $settings) {
		// parent::__construct();
		$this->papersModel = $papers;
		$this->settings = $settings;
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/SearchForm.latte');
		$this->template->render();
	}

	protected function createComponentSearchForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('number', 'Number:')->setRequired('Please set number');
		$form->addSelect('type', 'Type', $this->settings->params['types'])
				->setPrompt('All');
		$form->addSelect('year', 'Year', $this->settings->params['years'])
				->setPrompt('All');
		$form->addSubmit('save', 'Search');
		$form->onSuccess[] = $this->searchFormSucceeded;
		return $form;
	}

	public function searchFormSucceeded(UI\Form $form, Nette\Utils\ArrayHash $vals) {
		if (isset($vals['number'])) {
			return $vals;
		} else {
			return $this->redirect('this');
		}
	}

}

interface ISearchFormFactory {
	/** @return SearchForm */
	function create();
}
