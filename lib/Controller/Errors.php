<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Hammad Rashid <hrrashid.c@stc.com.sa>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\StcCustomScripts\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\StcCustomScripts\Service\NoteNotFound;

trait Errors {
	protected function handleNotFound(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (NoteNotFound $e) {
			$message = ['message' => $e->getMessage()];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);
		}
	}
}
