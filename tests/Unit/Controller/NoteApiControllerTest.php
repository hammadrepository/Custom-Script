<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Hammad Rashid <hrrashid.c@stc.com.sa>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\StcCustomScripts\Tests\Unit\Controller;

use OCA\StcCustomScripts\Controller\NoteApiController;

class NoteApiControllerTest extends NoteControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new NoteApiController($this->request, $this->service, $this->userId);
	}
}
