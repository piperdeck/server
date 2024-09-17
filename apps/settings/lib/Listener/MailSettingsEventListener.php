<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Settings\Listener;

use OCA\Settings\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\DeclarativeSettingsTypes;
use OCP\Settings\Events\DeclarativeSettingsGetValueEvent;
use OCP\Settings\Events\DeclarativeSettingsRegisterFormEvent;
use OCP\Settings\Events\DeclarativeSettingsSetValueEvent;

/** @template-implements IEventListener<DeclarativeSettingsRegisterFormEvent> */
class MailSettingsEventListener implements IEventListener {

	public function __construct(
		private IConfig $config,
		private IL10N $l,
	) {
	}

	public function handle(Event $event): void {

		if ($event instanceof DeclarativeSettingsRegisterFormEvent) {
			$this->handleRegister($event);
			return;
		}

		if ($event->getApp() !== Application::APP_ID) {
			return;
		}

		if ($event instanceof DeclarativeSettingsGetValueEvent) {
			$this->handleGetValue($event);
			return;
		}

		if ($event instanceof DeclarativeSettingsSetValueEvent) {
			$this->handleSetValue($event);
			return;
		}
		
	}

	private function handleRegister(Event $event) {

		$event->registerSchema(Application::APP_ID, [
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
		]);

	}

	private function handleGetValue(Event $event) {
		
		$event->setValue(
			match($event->getFieldId()) {
				'mail_providers_disabled' => $this->config->getSystemValueInt('mail_providers_disabled', 0),
			}
		);

	}

	private function handleSetValue(Event $event) {

		switch ($event->getFieldId()) {
			case 'mail_providers_disabled':
				$this->config->setSystemValue('mail_providers_disabled', $event->getValue());
				$event->stopPropagation();
				break;
		}

	}

}
