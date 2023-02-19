<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\StcCustomScripts\Command;

use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class SetBulkUsersQuota extends Command {


	const COMMAND_DEV = "php occ user:setting %s files quota %s";
	const COMMAND_STC_DEV = "sudo -u apache php occ user:setting %s files quota %s";
	const COMMAND_STC_PROD = "sudo -u apache php occ user:setting %s files quota %s";
	private string $COMMAND_FINAL = "";
	/**
	 * @param SingleUser $single
	 * @param AllUsers $all
	 * @param IUserManager $userManager
	 */
	public function __construct() {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('stccustomscripts:set-bulk-users-quota')
			->setDescription(
				'Set bulk users quota')
			->addArgument(
				'file-path',
				InputArgument::REQUIRED,
				'File location of CSV'
				)
			->addArgument(
				'env',
				InputArgument::REQUIRED,
				'Environment: dev,stc_dev,stc_prod'
				);
	}

	protected function setCommand(string $command) : string{
		switch ($command) {
			case 'dev':
				return self::COMMAND_DEV;
			case 'stc_dev':
				return self::COMMAND_STC_DEV;
			case 'stc_prod':
					return self::COMMAND_STC_PROD;
			default:
				throw new \InvalidArgumentException('Unknown Environment');
		}
	}

	protected function checkInput(InputInterface $input) {
		$file = $input->getArgument('file-path');
		if (!file_exists($file)) {
			throw new \InvalidArgumentException('File does not exists! Write full path of the file.');
		}

		if(!preg_match("/\.(csv)$/", $file)){
			throw new \InvalidArgumentException('File not valid. Only CSV file is allowed!');	
		}
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		// if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {

		// }

		if ($input->getArgument('env')) {
			$this->COMMAND_FINAL = $this->setCommand($input->getArgument('env'));
		}
		
		try {
			$this->checkInput($input);
		} catch (\InvalidArgumentException $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return 1;
		}
		$failedUsers = [];	

		if ($input->getArgument('file-path')) {
			$handle = fopen((string) $input->getArgument('file-path'), "r");
			for ($i = 0; $row = fgetcsv($handle ); ++$i) {
			try {
					$column1 = $row[0];
					$column2 = $row[1];
					if(!empty($column1) && !empty($column2)){
						$s = sprintf($this->COMMAND_FINAL, $column1,$column2);

						// regex to validate input like 20GB
						$regex = '/^\d(?:(?:\d\d\dG|(?:\d(?:\d)?G|G))|\.\dG)B$/m';
						if(!preg_match($regex, $column2)){
							$response = "fails";
						}else{
						$response = shell_exec($s);
						}
						if(empty($response)){
							$output->writeln("<info>{$column1}'s {$column2} quota has been set!</info>");
						}else if($response == "fails"){
							$output->writeln("<error>Invalid quota value for user {$column1}. Value must be like 5GB</error>");
						}
						else{
							array_push($failedUsers, $column1);
							$output->writeln("<error>The user {$column1} does not exist.</error>");
						}
				}

				} catch (\RuntimeException $ex) {
			    	die(sprintf('Error %s with code %d', $ex->getMessage(), $ex->getCode()));
				}
			}
			$output->writeln("");
			$output->writeln("<comment>Summary: Following users quota couldn't be set!</comment>");
			foreach($failedUsers as $user){
				$output->writeln("<fg=white>{$user}</>");
			}
			fclose($handle);
		} else {
			$output->writeln("Something went wrong!");
		}

		return 0;
	}


}
