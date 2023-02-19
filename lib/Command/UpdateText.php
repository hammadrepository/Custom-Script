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

class UpdateText extends Command {

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
			->setName('stccustomscripts:update-user-profile-picture')
			->setDescription(
				'Prints a CSV entry with some usage information of the user:' . "\n"
				. 'userId,date,assignedQuota,usedQuota,numFiles,numShares,numUploads,numDownloads' . "\n"
				. '"admin","2017-09-18T09:00:01+00:00",5368709120,786432000,1024,23,1400,5678'
			)
			->addArgument(
				'user-id',
				InputArgument::REQUIRED,
				'User to update the profile picture on next login'
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

		if ($input->getArgument('user-id')) {
			$update = $this->getUpdateQuery($input->getArgument('user-id'));
			$updated = $update->executeStatement();
			$output->writeln($updated);
		} else {
			$output->writeln("User not found");
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
