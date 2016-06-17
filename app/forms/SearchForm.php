<?php

namespace Form;

use Nette;
use Nette\Application\UI;
use App\Model;

class SearchForm extends UI\Control {

	public $onSave = [];

	/** @var \Model\Papers */
	private $papersModel;

	public function __construct(Model\Papers $papers) {
		parent::__construct();
		$this->papersModel = $papers;
	}

	public function render() {
		$this->template->setFile(__DIR__ . '/SearchForm.latte');
		$this->template->render();
	}

	protected function createComponentSearchForm() {
		$form = new UI\Form;
		$form->addProtection();
		$form->addText('number', 'Number:')->setRequired('Je zapotřebí vyplnit titulek.');
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
