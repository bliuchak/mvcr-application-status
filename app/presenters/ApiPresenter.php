<?php

namespace App\Presenters;

use App\Model\Papers;

class ApiPresenter extends BasePresenter {

	/** @var papersModel */
	private $papersModel;

	public function __construct(Papers $papersModel) {
		$this->papersModel = $papersModel;
	}

	public function actionUpdate() {
		return $this->papersModel->updateDatabase();
	}

}
