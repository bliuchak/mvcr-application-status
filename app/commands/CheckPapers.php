<?php
namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPapers extends Command {

	protected function configure() {
		$this->setName('papers:check')
					->setDescription('Get papers status from the server')
					->addArgument(
							'paperNumber',
							InputArgument::REQUIRED,
							'Paper number'
						)
					->addOption(
							'type',
							null,
							InputOption::VALUE_OPTIONAL,
							'Define type for paper number (long term, employee card, permanent residence or all)'
					)
					->addOption(
							'year',
							null,
							InputOption::VALUE_OPTIONAL,
							'Year when application was issues'
					);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$papersModel = $this->getHelper('container')->getByType('App\Model\Papers');
		$paperNumber = $input->getArgument('paperNumber');
		$type = $input->getOption('type');
		$year = $input->getOption('year');
		try {
			$check = $papersModel->getByNumber($paperNumber, $type, $year);
			$output->writeLn('<bg=cyan>Results:</>');
			if (count($check)) {
				foreach ($check as $data) {
					$output->writeLn($data->rawNumber);
				}
			}
			$output->writeLn('<bg=red>End</>');
			return 0; // zero return code means everything is ok
		} catch (\Nette\Mail\SmtpException $e) {
			$output->writeLn('<error>' . $e->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
