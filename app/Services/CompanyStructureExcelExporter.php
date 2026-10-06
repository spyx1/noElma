<?php

namespace App\Services;

use App\Models\CompanyStructureNode;
use Phar;
use PharData;
use RuntimeException;

class CompanyStructureExcelExporter
{
    /**
     * @param iterable<CompanyStructureNode> $roots
     */
    public function export(iterable $roots): string
    {
        $rows = [];
        $maxDepth = 0;

        foreach ($roots as $root) {
            $this->appendNodeRows($root, 0, $rows, $maxDepth);
        }

        $tmpBase = tempnam(sys_get_temp_dir(), 'company_structure_');
        if ($tmpBase === false) {
            throw new RuntimeException('Не удалось создать временный файл для экспорта структуры.');
        }

        @unlink($tmpBase);
        $zipPath = $tmpBase . '.zip';
        $xlsxPath = $tmpBase . '.xlsx';

        try {
            $archive = new PharData($zipPath, 0, null, Phar::ZIP);

            $archive->addFromString('[Content_Types].xml', $this->contentTypesXml());
            $archive->addFromString('_rels/.rels', $this->rootRelationsXml());
            $archive->addFromString('xl/workbook.xml', $this->workbookXml());
            $archive->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationsXml());
            $archive->addFromString('xl/styles.xml', $this->stylesXml());
            $archive->addFromString(
                'xl/worksheets/sheet1.xml',
                $this->worksheetXml($rows, min(7, $maxDepth))
            );

            unset($archive);

            if (!@rename($zipPath, $xlsxPath)) {
                throw new RuntimeException('Не удалось подготовить XLSX-файл структуры.');
            }

            return $xlsxPath;
        } catch (\Throwable $e) {
            @unlink($zipPath);
            @unlink($xlsxPath);
            throw $e;
        }
    }

    /**
     * @param array<int, array{title:string,employee:string,depth:int}> $rows
     */
    private function appendNodeRows(
        CompanyStructureNode $node,
        int $depth,
        array &$rows,
        int &$maxDepth
    ): void {
        $depth = min(7, max(0, $depth));
        $maxDepth = max($maxDepth, $depth);

        $users = $node->users
            ->sortBy(fn ($user) => $user->displayName())
            ->values();

        if ($users->isEmpty()) {
            $rows[] = [
                'title' => $node->title,
                'employee' => '',
                'depth' => $depth,
            ];
        } else {
            foreach ($users as $user) {
                $rows[] = [
                    'title' => $node->title,
                    'employee' => $user->displayName(),
                    'depth' => $depth,
                ];
            }
        }

        foreach ($node->childrenRecursive as $child) {
            $this->appendNodeRows($child, $depth + 1, $rows, $maxDepth);
        }
    }

    private function worksheetXml(array $rows, int $maxDepth): string
    {
        $lastRow = count($rows) + 1;
        $xmlRows = [];

        $xmlRows[] = '<row r="1" ht="22" customHeight="1">'
            . $this->inlineStringCell('A1', 'Должность/Подразделение', 1)
            . $this->inlineStringCell('B1', 'Сотрудник', 1)
            . '</row>';

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $depth = min(7, max(0, (int) $row['depth']));
            $titleStyle = 2 + $depth;

            $xmlRows[] = '<row r="' . $rowNumber . '" ht="18" customHeight="1" outlineLevel="' . $depth . '">'
                . $this->inlineStringCell('A' . $rowNumber, $row['title'], $titleStyle)
                . $this->inlineStringCell('B' . $rowNumber, $row['employee'], 2)
                . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetPr><outlinePr summaryBelow="0" summaryRight="0"/></sheetPr>'
            . '<dimension ref="A1:B' . max(1, $lastRow) . '"/>'
            . '<sheetViews><sheetView workbookViewId="0" showGridLines="0">'
            . '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
            . '</sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18" outlineLevelRow="' . $maxDepth . '"/>'
            . '<cols><col min="1" max="1" width="56" customWidth="1"/><col min="2" max="2" width="42" customWidth="1"/></cols>'
            . '<sheetData>' . implode('', $xmlRows) . '</sheetData>'
            . '<autoFilter ref="A1:B' . max(1, $lastRow) . '"/>'
            . '</worksheet>';
    }

    private function inlineStringCell(string $reference, string $value, int $style): string
    {
        $escaped = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<c r="' . $reference . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">'
            . $escaped
            . '</t></is></c>';
    }

    private function stylesXml(): string
    {
        $bodyXf = static fn (int $indent): string =>
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyAlignment="1">'
            . '<alignment vertical="center" horizontal="left" wrapText="0" indent="' . $indent . '"/>'
            . '</xf>';

        $xfs = [
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>',
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyAlignment="1">'
                . '<alignment vertical="center" horizontal="left"/>'
                . '</xf>',
        ];

        for ($indent = 0; $indent <= 7; $indent++) {
            $xfs[] = $bodyXf($indent);
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FF31506B"/><name val="Calibri"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="3">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFEAF1F5"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFD4E0E8"/></left>'
            . '<right style="thin"><color rgb="FFD4E0E8"/></right>'
            . '<top style="thin"><color rgb="FFD4E0E8"/></top>'
            . '<bottom style="thin"><color rgb="FFD4E0E8"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="' . count($xfs) . '">' . implode('', $xfs) . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRelationsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Оргструктура" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelationsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }
}
