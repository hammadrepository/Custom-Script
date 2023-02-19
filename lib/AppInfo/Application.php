<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Hammad Rashid <hrrashid.c@stc.com.sa>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\StcCustomScripts\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'stccustomscripts';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
