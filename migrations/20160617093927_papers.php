<?php

use Phinx\Migration\AbstractMigration;

class Papers extends AbstractMigration {

	public function change() {
		$table = $this->table('papers');
		$table->addColumn('rawNumber', 'string', ['limit' => 64])
					->addColumn('number', 'string', ['limit' => 10])
					->addColumn('type', 'enum', ['values' => ['DP', 'PP', 'DV', 'ZM', 'TP']])
					->addColumn('year', 'integer', ['limit' => 4])
					->addColumn('created', 'integer')
					->addColumn('deleted', 'integer', ['null' => true])
					->create();
	}
}
