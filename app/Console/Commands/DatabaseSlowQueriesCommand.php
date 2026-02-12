<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseSlowQueriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:slow-queries
                            {--limit=20 : Number of slowest queries to display}
                            {--threshold=1000 : Minimum execution time in milliseconds}
                            {--today : Only show queries from today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display slow database queries from query log';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $logPath = storage_path('logs/queries-'.now()->format('Y-m-d').'.log');

        if (! File::exists($logPath)) {
            $this->error('Query log file not found: '.$logPath);
            $this->info('Make sure query logging is enabled in your Query classes.');

            return self::FAILURE;
        }

        $logContent = File::get($logPath);
        $entries = $this->parseLogEntries($logContent);

        if (empty($entries)) {
            $this->info('No query log entries found.');

            return self::SUCCESS;
        }

        // Filter by threshold
        $threshold = (int) $this->option('threshold');
        $filtered = collect($entries)->filter(function ($entry) use ($threshold) {
            return isset($entry['duration']) && $entry['duration'] >= $threshold;
        });

        if ($filtered->isEmpty()) {
            $this->info("No queries found exceeding {$threshold}ms threshold.");
            $this->info('Total queries logged: '.count($entries));

            return self::SUCCESS;
        }

        // Sort by duration (slowest first)
        $sorted = $filtered->sortByDesc('duration');

        // Limit results
        $limit = (int) $this->option('limit');
        $results = $sorted->take($limit);

        // Display results
        $this->displayResults($results, $filtered->count());

        return self::SUCCESS;
    }

    /**
     * Parse log file entries
     */
    protected function parseLogEntries(string $content): array
    {
        $entries = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Parse log line (Laravel daily log format)
            if (
                preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?: (.+Query SQL)/', $line, $matches)
            ) {
                $timestamp = $matches[1];
                $message = $matches[2];

                // Try to extract duration if logged
                $duration = null;
                if (preg_match('/duration["\']?\s*[:=]\s*["\']?(\d+\.?\d*)/', $line, $durationMatch)) {
                    $duration = (float) $durationMatch[1];
                }

                // Extract SQL if present
                $sql = null;
                if (preg_match('/"sql":\s*"([^"]+)"/', $line, $sqlMatch)) {
                    $sql = $sqlMatch[1];
                }

                $entries[] = [
                    'timestamp' => $timestamp,
                    'message' => $message,
                    'duration' => $duration,
                    'sql' => $sql,
                ];
            }
        }

        return $entries;
    }

    /**
     * Display results in table format
     */
    protected function displayResults($results, int $totalFiltered): void
    {
        $this->newLine();
        $this->info('=== Slow Database Queries ===');
        $this->info("Showing {$results->count()} of {$totalFiltered} queries");
        $this->newLine();

        $tableData = [];
        foreach ($results as $entry) {
            $tableData[] = [
                'Time' => $entry['timestamp'],
                'Duration (ms)' => number_format($entry['duration'] ?? 0, 2),
                'Query' => $entry['message'],
                'SQL' => $this->truncateSql($entry['sql'] ?? 'N/A'),
            ];
        }

        $this->table(['Time', 'Duration (ms)', 'Query', 'SQL Preview'], $tableData);

        // Summary statistics
        $this->displayStatistics($results);
    }

    /**
     * Display summary statistics
     */
    protected function displayStatistics($results): void
    {
        $durations = $results->pluck('duration')->filter();

        if ($durations->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->info('=== Statistics ===');
        $this->info('Average Duration: '.number_format($durations->avg(), 2).'ms');
        $this->info('Median Duration: '.number_format($durations->median(), 2).'ms');
        $this->info('Max Duration: '.number_format($durations->max(), 2).'ms');
        $this->info('Min Duration: '.number_format($durations->min(), 2).'ms');
        $this->newLine();

        // Performance interpretation
        $avgDuration = $durations->avg();
        if ($avgDuration > 5000) {
            $this->warn('⚠️  Very Slow: Average query time > 5 seconds - immediate optimization needed');
        } elseif ($avgDuration > 2000) {
            $this->warn('⚠️  Slow: Average query time > 2 seconds - optimization recommended');
        } elseif ($avgDuration > 1000) {
            $this->comment('⚡ Moderate: Average query time > 1 second - consider optimization');
        } else {
            $this->info('✓ Good: Average query time < 1 second');
        }
    }

    /**
     * Truncate SQL for display
     */
    protected function truncateSql(?string $sql): string
    {
        if (! $sql) {
            return 'N/A';
        }

        return strlen($sql) > 100 ? substr($sql, 0, 100).'...' : $sql;
    }
}
