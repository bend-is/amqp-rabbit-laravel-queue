<?php

declare(strict_types = 1);

namespace AvtoDev\AmqpRabbitLaravelQueue\Commands;

use Illuminate\Contracts\Queue\Job;
use AvtoDev\AmqpRabbitLaravelQueue\Worker;
use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * You should NOT register this command in console kernel.
 *
 * @see \AvtoDev\AmqpRabbitLaravelQueue\ServiceProvider::overrideQueueWorkerCommand()
 */
class WorkCommand extends \Illuminate\Queue\Console\WorkCommand
{
    /**
     * Create a new queue work command.
     *
     * @param Worker $worker
     * @param Cache  $cache
     */
    public function __construct(Worker $worker, Cache $cache)
    {
        // Override default timeout value ('60' to '-1')
        $this->signature = (string) \preg_replace(
            '~(--timeout=)\d+~', '$1-1', $this->signature
        );

        // Mark 'sleep' option as not used
        $this->signature = (string) \preg_replace(
            '~(--sleep.*)}~', '$1 <options=bold>(not used)</> }', $this->signature
        );

        parent::__construct($worker, $cache);
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function line($string, $style = null, $verbosity = null): void
    {
        $time = (new \DateTime)->format('H:i:s.v');

        $styled = $style
            ? "<$style>$string</$style>"
            : $string;

        $this->output->writeln("<fg=white>{$time}</> {$styled}", $this->parseVerbosity($verbosity));
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    protected function writeStatus(Job $job, $status, $type): void
    {
        $this->line(sprintf(
            "<{$type}>[%s] %s</{$type}> %s",
            $job->getJobId(),
            \str_pad("{$status}:", 11), $job->resolveName()
        ));
    }

    /**
     * Run the worker instance.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return array<mixed>
     *
     * @codeCoverageIgnore
     */
    protected function runWorker($connection, $queue): array
    {
        $this->info('Queue worker started. Press "CTRL+C" to exit');

        return parent::runWorker($connection, $queue);
    }
}
