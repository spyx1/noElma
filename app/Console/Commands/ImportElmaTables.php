<?php

namespace App\Console\Commands;

use App\Models\ReferenceTable;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ImportElmaTables extends Command
{
    protected $signature = 'tables:import-elma {file : Путь к XLSX-выгрузке ELMA} {--fresh : Очистить справочник перед импортом} {--dry-run : Проверить файл без записи в БД}';

    protected $description = 'Импортирует шаблоны таблиц из XLSX-выгрузки ELMA 365';

    public function handle(): int
    {
        $file = $this->argument('file');
        if (! is_file($file)) {
            $this->error("Файл не найден: {$file}");
            return self::FAILURE;
        }

        try {
            $rows = $this->readWorkbook($file);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $tables = [];
        $skipped = 0;
        foreach ($rows as $row) {
            $json = json_decode($row['Таблица JSON [table_json]'] ?? '', true);
            $name = trim((string) ($row['Название [__name]'] ?? ''));
            if (! is_array($json) || $name === '') {
                $skipped++;
                continue;
            }

            $externalId = trim((string) ($row['Идентификатор [__id]'] ?? $json['id'] ?? ''));
            if ($externalId === '') {
                $skipped++;
                continue;
            }

            $tables[] = [
                'external_id' => $externalId,
                'name' => $name,
                'row_count' => count($json['dv'] ?? []) ?: (int) ($row['Количество строк [count_rows]'] ?? 0),
                'script' => $row['Скрипт [script]'] ?: ($json['script'] ?? null),
                'default_values' => array_values($json['dv'] ?? []),
                'default_result_values' => array_values($json['drv'] ?? []),
                'source_json' => $json,
            ];
        }

        if ($this->option('dry-run')) {
            $this->info("Будет импортировано таблиц: ".count($tables).". Пропущено строк: {$skipped}.");
            return self::SUCCESS;
        }

        if (! $this->option('fresh')) {
            $this->error('Для импорта требуется --fresh: команда заменит содержимое справочника «Таблицы».');
            return self::FAILURE;
        }

        $userId = User::query()->value('id');
        DB::transaction(function () use ($tables, $userId): void {
            ReferenceTable::query()->delete();

            foreach ($tables as $attributes) {
                $table = ReferenceTable::create([...$attributes, 'created_by' => $userId, 'updated_by' => $userId]);
                $this->createColumns($table, $attributes['source_json']['columns'] ?? [], 'primary', $attributes['default_values']);
                $this->createColumns($table, $attributes['source_json']['result_columns'] ?? [], 'result', $attributes['default_result_values']);
            }
        });

        $this->info("Импортировано таблиц: ".count($tables).". Пропущено строк: {$skipped}.");
        return self::SUCCESS;
    }

    private function createColumns(ReferenceTable $table, array $columns, string $section, array $defaults): void
    {
        foreach ($columns as $position => $column) {
            $type = (string) ($column['type'] ?? 'string');
            $values = $section === 'primary'
                ? array_map(fn (array $row) => $row[$position] ?? '', $defaults)
                : [];

            $table->columns()->create([
                'section' => $section,
                'name' => (string) ($column['name'] ?? ''),
                'data_type' => $this->russianType($type),
                'elma_type' => $type,
                'is_required' => (bool) ($column['required'] ?? false),
                'is_readonly' => (bool) ($column['readonly'] ?? false),
                'is_key' => false,
                'width' => max(40, (int) ($column['w'] ?? 175)),
                'values' => $values,
                'settings' => $column['data'] ?? [],
                'position' => $position + 1,
            ]);
        }
    }

    private function russianType(string $type): string
    {
        return match ($type) {
            'integer' => 'Число целое',
            'float' => 'Число дробное',
            'boolean' => 'Логическое',
            'select' => 'Список',
            'files' => 'Файлы',
            'app' => 'Приложение',
            'inc' => 'Итератор',
            default => 'Строка',
        };
    }

    private function readWorkbook(string $file): array
    {
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            throw new RuntimeException('Не удалось открыть XLSX-файл.');
        }

        $shared = $this->readSharedStrings((string) $zip->getFromName('xl/sharedStrings.xml'));
        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($xml === false) {
            throw new RuntimeException('В XLSX не найден первый лист.');
        }

        $sheet = new SimpleXMLElement($xml);
        $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $headers = [];
        $rows = [];

        foreach ($sheet->xpath('//x:sheetData/x:row') as $rowIndex => $row) {
            $row->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $cells = [];
            foreach ($row->xpath('./x:c') as $cell) {
                $reference = (string) $cell['r'];
                $column = preg_replace('/\\d+/', '', $reference);
                $cells[$column] = $this->cellValue($cell, $shared);
            }
            if ($rowIndex === 0) {
                $headers = $cells;
                continue;
            }
            $record = [];
            foreach ($headers as $column => $header) {
                $record[$header] = $cells[$column] ?? '';
            }
            $rows[] = $record;
        }

        return $rows;
    }

    private function readSharedStrings(string $xml): array
    {
        if ($xml === '') {
            return [];
        }
        $strings = new SimpleXMLElement($xml);
        $strings->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        return array_map(function (SimpleXMLElement $item): string {
            $item->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            return implode('', array_map('strval', $item->xpath('.//x:t')));
        }, $strings->xpath('//x:si'));
    }

    private function cellValue(SimpleXMLElement $cell, array $shared): string
    {
        $type = (string) $cell['t'];
        if ($type === 's') {
            return $shared[(int) $cell->v] ?? '';
        }
        if ($type === 'inlineStr') {
            $cell->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            return implode('', array_map('strval', $cell->xpath('.//x:t')));
        }
        return (string) $cell->v;
    }
}
