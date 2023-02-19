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

class CreateBulkUsers extends Command {

	/** @var SingleUser */
	protected $single;

	/** @var AllUsers */
	protected $all;

	/** @var IUserManager */
	protected $userManager;

	protected $connection;

	/**
	 * @param SingleUser $single
	 * @param AllUsers $all
	 * @param IUserManager $userManager
	 */
	public function __construct(IUserManager $userManager,IDBConnection $connection) {
		parent::__construct();

		// $this->single = $single;
		// $this->all = $all;
		$this->userManager = $userManager;
		$this->connection = $connection;
	}

	protected function configure(): void {
		$this
			->setName('stccustomscripts:create-bulk-users')
			->setDescription(
				'Create bulk users into nextcloud from csv file format')
			->addArgument(
				'file-path',
				InputArgument::REQUIRED,
				'File location of CSV'
			// ->addOption(
			// 	'field-separator',
			// 	'',
			// 	InputOption::VALUE_REQUIRED,
			// 	'Separator for the fields in the list',
			// 	','
			// )
			// ->addOption(
			// 	'date-format',
			// 	'',
			// 	InputOption::VALUE_REQUIRED,
			// 	'Date format of the entries (see http://php.net/manual/en/function.date.php for more information)',
			// 	'c'
			 )
		;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		// if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {

		// }
		$curl = new Curl\CurlGet('http://127.0.0.1/nextcloud/ocs/v1.php/cloud/users',[],[
			'OCS-APIRequest:true'
		]);
		if ($input->getArgument('file-path')) {
			$handle = fopen("/home/parallels/Downloads/Sample.csv", "r");
			for ($i = 0; $row = fgetcsv($handle ); ++$i) {
			try {
	    		// execute the request
				    echo $curl([
				        'userid' => 'Mousa',
				        'email' => 'mousa@gmail.com',
				    ]);
				} catch (\RuntimeException $ex) {
			    // catch errors
			    die(sprintf('Http error %s with code %d', $ex->getMessage(), $ex->getCode()));
				}
			}
			fclose($handle);
		} else {
			$output->writeln("File not found");
		}

		return 0;
	}



	protected function getUpdateQuery($user_ID): IQueryBuilder {
		// if ($this->update !== null) {
		// 	return $this->update;
		// }

		$query = $this->connection->getQueryBuilder();
		$query->update('preferences')
			->set('configvalue',$query->createParameter('val'))
			->where($query->expr()->eq('userid', $query->createParameter('user_id')))
			->andWhere($query->expr()->eq('configkey', $query->createParameter('action')))
			->andWhere($query->expr()->eq('appid', $query->createParameter('appid')))
			->setParameter('action', 'workspace_enabled')
			->setParameter('user_id', $user_ID)
			->setParameter('val', 1)
			->setParameter('appid', 'text');
		$this->update = $query;

		return $query;
	}

}
