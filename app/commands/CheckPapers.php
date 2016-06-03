<?php
namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPapers extends Command {

	protected function configure() {
		$this->setName('papers:check')
					->setDescription('Get papers status from the server')
					->addArgument(
							'paperNumber',
							InputArgument::REQUIRED,
							'Paper number'
						);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$papersModel = $this->getHelper('container')->getByType('App\Model\Papers');
		$paperNumber = $input->getArgument('paperNumber');
		try {
			if ($paperNumber && $papersModel->check($paperNumber)) {
				$output->writeLn('Possible matches: '.$papersModel->check($paperNumber));
			} else {
				$output->writeLn('Empty result');
			}
			return 0; // zero return code means everything is ok

		} catch (\Nette\Mail\SmtpException $e) {
			$output->writeLn('<error>' . $e->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
