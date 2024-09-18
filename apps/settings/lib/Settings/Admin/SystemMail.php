<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Settings\Settings\Admin;

use OCP\IL10N;
use OCP\Settings\DeclarativeSettingsTypes;
use OCP\Settings\IDeclarativeSettingsForm;

class SystemMail implements IDeclarativeSettingsForm {

    public function __construct(
        private IL10N $l
    ) {}

	public function getSchema(): array {
		return [
			'id' => 'mail-provider-support',
			'priority' => 11,
			'section_type' => DeclarativeSettingsTypes::SECTION_TYPE_ADMIN,
			'section_id' => 'server',
			'storage_type' => DeclarativeSettingsTypes::STORAGE_TYPE_EXTERNAL,
			'title' => $this->l->t('System Mails'),
			'description' => $this->l->t('System e-mails are messages generated automatically by Nextcloud. They are sent for example when a share is created or when inviting attendees to a calendar event..'),

			'fields' => [
				[
					'id' => 'mail_providers_disabled',
					'title' => $this->l->t('Send system e-mails using'),
					'type' => DeclarativeSettingsTypes::RADIO,
					'default' => 0,
					'options' => [
						[
							'name' => $this->l->t('People\'s configured mail account'),
							'value' => 0
						],
						[
							'name' => $this->l->t('System account'),
							'value' => 1
						],
					],
				],
			],
		];
	}

}
