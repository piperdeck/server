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
use OCP\Settings\Events\DeclarativeSettingsRegisterFormEvent;
use OCP\Settings\Events\DeclarativeSettingsSetValueEvent;
use OCP\Settings\Events\DeclarativeSettingsGetValueEvent;

/** @template-implements IEventListener<DeclarativeSettingsRegisterFormEvent> */
class MailSettingsEventListener implements IEventListener {

	public function __construct(
		private IConfig $config,
		private IL10N $l,
	) {
	}

	public function handle(Event $event): void {

		if ($event->getApp() !== Application::APP_ID) {
			return;
		}

		if ($event instanceof DeclarativeSettingsRegisterFormEvent) {
			$this->handleRegister($event);
		}

		if ($event instanceof DeclarativeSettingsGetValueEvent) {
			$this->handleGetValue($event);
		}

		if ($event instanceof DeclarativeSettingsSetValueEvent) {
			$this->handleSetValue($event);
		}
		
	}

	private function handleRegister(Event $event) {

		$event->registerSchema(Application::APP_ID, [
			'id' => 'mail-provider-support',
			'priority' => 10,
			'section_type' => DeclarativeSettingsTypes::SECTION_TYPE_ADMIN,
			'section_id' => 'server',
			'storage_type' => DeclarativeSettingsTypes::STORAGE_TYPE_EXTERNAL,
			'title' => $this->l->t('System Mails'),
			'description' => $this->l->t('Allow to restrict filenames to ensure files can be synced with all clients. By default all filenames valid on POSIX (e.g. Linux or macOS) are allowed.'),

			'fields' => [
				[
					'id' => 'mail_providers_enabled',
					'title' => $this->l->t('Allow system to use configured user mail accounts'),
					'description' => $this->l->t('This will block filenames not valid on Windows systems, like using reserved names or special characters. But this will not enforce compatibility of case sensitivity.'),
					'type' => DeclarativeSettingsTypes::CHECKBOX,
					'default' => false,
				],
			],
		]);

	}

	private function handleGetValue(Event $event) {
		
		$event->setValue(
			match($event->getFieldId()) {
				'mail_providers_enabled' => $this->config->getValueBool('core', 'mail_providers_enabled', true),
			}
		);

	}

	private function handleSetValue(Event $event) {

		switch ($event->getFieldId()) {
			case 'mail_providers_enabled':
				$this->config->setValueBool('core', 'mail_providers_enabled', (bool)$event->getValue());
				$event->stopPropagation();
				break;
		}

	}

}
