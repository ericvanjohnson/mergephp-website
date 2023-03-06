<?php

declare(strict_types=1);

namespace MergePHP\Website\Builder\Processor;

use DateTimeImmutable;
use MergePHP\Website\Builder\MeetupCollection;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class HomepageProcessor extends HTMLProcessor
{
	public function __construct(
		protected LoggerInterface $logger,
		protected string $outputDirectory,
		protected MeetupCollection $meetups,
		protected Environment $twig,
	) {
		parent::__construct($logger, $this->outputDirectory);
	}

	public function run(): void
	{
		$this->logger->info('Building homepage');
		$futureMeetups = $this->meetups->withOnlyFuture();
		$pastMeetups = $this->meetups->withOnlyPast();

		$data = [
			'nextMeetup'  => reset($futureMeetups)->instance,
			'archiveYear' => end($pastMeetups)->instance->getDateTime()->format('Y'),
		];

		$this->logger->debug("Building homepage with reference to {$data['nextMeetup']?->getTitle()}");

		/** @noinspection PhpUnhandledExceptionInspection */
		$html = $this->twig->render('index.twig.html', $data);
		$this->writeHtml($html, 'index.html', new DateTimeImmutable());
	}
}