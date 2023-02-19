<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Hammad Rashid <hrrashid.c@stc.com.sa>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\StcCustomScripts\Controller;

use OCA\StcCustomScripts\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

class PageController extends Controller {
	public function __construct(IRequest $request) {
		parent::__construct(Application::APP_ID, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
		Util::addScript(Application::APP_ID, 'stccustomscripts-main');

		return new TemplateResponse(Application::APP_ID, 'main');
	}
}
