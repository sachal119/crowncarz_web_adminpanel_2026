<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ProcessFixedPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 1;
    public bool $failOnTimeout = true;

    public function __construct(
        private readonly string $filePath,
        private readonly string $importId,
    ) {
    }

    public function handle(): void
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');
        $spreadsheet = null;

        try {
            $this->progress(7, 'Background worker is reading the spreadsheet...');
            $absolutePath = Storage::disk('local')->path($this->filePath);
            $reader = IOFactory::createReaderForFile($absolutePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($absolutePath);
            $sheet = $spreadsheet->getActiveSheet();
            $lastRow = $sheet->getHighestDataRow();

            if ($lastRow < 2) {
                throw new \RuntimeException('The spreadsheet does not contain any pricing rows.');
            }

            $records = [];
            $skipped = 0;
            $duplicates = 0;
            $totalRows = max($lastRow - 1, 1);
            $chunkSize = 5000;
            $now = now();

            for ($startRow = 2; $startRow <= $lastRow; $startRow += $chunkSize) {
                $endRow = min($startRow + $chunkSize - 1, $lastRow);
                $rows = $sheet->rangeToArray("A{$startRow}:C{$endRow}", null, false, false);

                foreach ($rows as $offset => $row) {
                    $rowNumber = $startRow + $offset;
                    $from = $this->normalisePostcode($row[0] ?? null);
                    $to = $this->normalisePostcode($row[1] ?? null);

                    if ($from === '' && $to === '') {
                        $skipped++;
                        continue;
                    }
                    if ($from === '' || $to === '') {
                        throw new \RuntimeException("Row {$rowNumber} must contain both From and To postcodes.");
                    }

                    $pairKey = preg_replace('/\s+/', '', $from).'|'.preg_replace('/\s+/', '', $to);
                    if (isset($records[$pairKey])) {
                        $duplicates++;
                    }

                    $records[$pairKey] = [
                        'from_postcode' => $from,
                        'to_postcode' => $to,
                        'company_price' => $this->normalisePrice($row[2] ?? null, $rowNumber),
                        'driver_price' => 0,
                        'agent_commission' => 0,
                        'type' => 0,
                        'vehicle_type' => 0,
                        'unique_key' => $from.'_'.$to.'_0',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $read = min($endRow - 1, $totalRows);
                $percent = 8 + (int) floor(($read / $totalRows) * 42);
                $this->progress($percent, "Validated {$read} of {$totalRows} spreadsheet rows.", $read, $totalRows);
            }

            if ($records === []) {
                throw new \RuntimeException('No valid pricing rows were found in the spreadsheet.');
            }

            $spreadsheet->disconnectWorksheets();
            $spreadsheet = null;

            $total = count($records);
            $processed = 0;

            foreach (array_chunk($records, 1000, true) as $batch) {
                DB::transaction(function () use ($batch): void {
                    $placeholders = [];
                    $bindings = [];
                    foreach ($batch as $record) {
                        $placeholders[] = '(?, ?)';
                        $bindings[] = $record['from_postcode'];
                        $bindings[] = $record['to_postcode'];
                    }

                    DB::delete(
                        'DELETE FROM fixed_pricing WHERE (from_postcode, to_postcode) IN ('.implode(', ', $placeholders).')',
                        $bindings
                    );
                    DB::table('fixed_pricing')->insert(array_values($batch));
                });

                $processed += count($batch);
                $percent = 50 + (int) floor(($processed / $total) * 49);
                $this->progress($percent, "Saved {$processed} of {$total} postcode prices.", $processed, $total);
            }

            $message = "Imported {$total} postcode prices: existing pairs replaced and new pairs added.";
            if ($duplicates > 0 || $skipped > 0) {
                $message .= " {$duplicates} duplicate and {$skipped} blank row(s) were consolidated/skipped.";
            }

            $this->progress(100, $message, $total, $total, 'completed');
            Storage::disk('local')->delete($this->filePath);
        } catch (Throwable $exception) {
            if ($spreadsheet !== null) {
                $spreadsheet->disconnectWorksheets();
            }
            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $message = 'Import failed: '.($exception?->getMessage() ?? 'Unknown background job error.');
        $this->progress(0, $message, null, null, 'failed');
        Storage::disk('local')->delete($this->filePath);
    }

    private function progress(
        int $percent,
        string $message,
        ?int $processed = null,
        ?int $total = null,
        string $status = 'processing',
    ): void {
        Cache::put('fixed-price-import:'.$this->importId, [
            'status' => $status,
            'percent' => max(0, min(100, $percent)),
            'message' => $message,
            'processed' => $processed,
            'total' => $total,
        ], now()->addHours(6));
    }

    private function normalisePostcode(mixed $postcode): string
    {
        return strtoupper(trim((string) preg_replace('/\s+/', ' ', (string) ($postcode ?? ''))));
    }

    private function normalisePrice(mixed $value, int $row): float
    {
        if ($value === null || trim((string) $value) === '') {
            return 0.0;
        }

        $normalised = str_replace([',', '£'], '', trim((string) $value));
        if (!is_numeric($normalised)) {
            throw new \RuntimeException("Row {$row} has an invalid Saloon price.");
        }

        return round((float) $normalised, 2);
    }
}
