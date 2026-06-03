<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\PegawaiModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends BaseController
{
    private function getFilteredData()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $bukuTamuModel = new BukuTamuModel();

        // Get filter inputs
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $pegawaiId = $this->request->getGet('pegawai_id');
        $idAgenda = $this->request->getGet('id_agenda');

        $query = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan', 'left')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left');

        // Scope by department if Admin
        if (!$isSuperadmin) {
            $query->where('buku_tamu.kode_opd', $user->kode_opd);
        }

        // Apply filters
        if ($startDate) {
            $query->where('buku_tamu.waktu_datang >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('buku_tamu.waktu_datang <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $query->where('buku_tamu.status_kunjungan', $status);
        }
        if ($pegawaiId) {
            $query->where('buku_tamu.id_pegawai_tujuan', $pegawaiId);
        }
        if ($idAgenda === 'reguler') {
            $query->where('buku_tamu.id_agenda IS NULL');
        } elseif ($idAgenda) {
            $query->where('buku_tamu.id_agenda', $idAgenda);
        }

        return $query->orderBy('buku_tamu.waktu_datang', 'DESC')->findAll();
    }

    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $pegawaiModel = new PegawaiModel();

        // Fetch target employees for filter dropdown
        $pegBuilder = $pegawaiModel->where('status', 'aktif');
        if (!$isSuperadmin) {
            $pegBuilder->where('kode_opd', $user->kode_opd);
        }
        $data['pegawais'] = $pegBuilder->orderBy('nama', 'ASC')->findAll();

        // Fetch active agendas for filter dropdown
        $agendaModel = new \App\Models\AgendaModel();
        $agendaBuilder = $agendaModel->where('status', 'aktif');
        if (!$isSuperadmin) {
            $agendaBuilder->where('kode_opd', $user->kode_opd);
        }
        $data['agendas'] = $agendaBuilder->orderBy('nama_agenda', 'ASC')->findAll();

        $data['tamus'] = $this->getFilteredData();
        
        $data['filters'] = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date'   => $this->request->getGet('end_date'),
            'status'     => $this->request->getGet('status'),
            'pegawai_id' => $this->request->getGet('pegawai_id'),
            'id_agenda'  => $this->request->getGet('id_agenda'),
        ];
        $data['isSuperadmin'] = $isSuperadmin;

        return view('laporan/index', $data);
    }

    public function exportPdf()
    {
        $data['tamus'] = $this->getFilteredData();
        $data['user'] = auth()->user();
        $data['isSuperadmin'] = $data['user']->inGroup('superadmin');

        $idAgenda = $this->request->getGet('id_agenda');
        $agendaName = '';
        if ($idAgenda === 'reguler') {
            $agendaName = 'Tamu Reguler (Tanpa Agenda)';
        } elseif ($idAgenda) {
            $agendaModel = new \App\Models\AgendaModel();
            $agenda = $agendaModel->find($idAgenda);
            if ($agenda) {
                $agendaName = 'Agenda: ' . $agenda['nama_agenda'];
            }
        }
        $data['agendaName'] = $agendaName;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('laporan/pdf_template', $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output stream
        $fileName = 'laporan_kunjungan_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }

    public function exportExcel()
    {
        $tamus = $this->getFilteredData();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header Document Info
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN TAMU ELEKTRONIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Dinas Komunikasi dan Informatika Kabupaten Grobogan');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Dynamic Agenda Subheader
        $idAgenda = $this->request->getGet('id_agenda');
        $agendaName = '';
        if ($idAgenda === 'reguler') {
            $agendaName = 'Tamu Reguler (Tanpa Agenda)';
        } elseif ($idAgenda) {
            $agendaModel = new \App\Models\AgendaModel();
            $agenda = $agendaModel->find($idAgenda);
            if ($agenda) {
                $agendaName = 'Agenda: ' . $agenda['nama_agenda'];
            }
        }

        if (!empty($agendaName)) {
            $sheet->mergeCells('A3:L3');
            $sheet->setCellValue('A3', strtoupper($agendaName));
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('4F46E5'));
            $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        }

        // Set Table Headers
        $headers = [
            'A4' => 'No',
            'B4' => 'No. Referensi',
            'C4' => 'Nama Tamu',
            'D4' => 'NIK',
            'E4' => 'Instansi',
            'F4' => 'No. HP',
            'G4' => 'Alamat',
            'H4' => 'Keperluan',
            'I4' => 'Pegawai Tujuan',
            'J4' => 'Waktu Datang',
            'K4' => 'Waktu Pulang',
            'L4' => 'Status'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Style Table Headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('A4:L4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // Populate Table Data
        $rowIdx = 5;
        $no = 1;
        foreach ($tamus as $tamu) {
            $sheet->setCellValue('A' . $rowIdx, $no++);
            $sheet->setCellValue('B' . $rowIdx, '#' . $tamu['no_referensi']);
            $sheet->setCellValue('C' . $rowIdx, $tamu['nama_tamu']);
            $sheet->setCellValue('D' . $rowIdx, "'" . $tamu['nik']); // Force string to avoid scientific notation
            $sheet->setCellValue('E' . $rowIdx, $tamu['instansi']);
            $sheet->setCellValue('F' . $rowIdx, "'" . $tamu['no_hp']); // Force string
            $sheet->setCellValue('G' . $rowIdx, $tamu['alamat']);
            $keperluan = (!empty($tamu['id_agenda']) && !empty($tamu['nama_agenda']))
                         ? 'Menghadiri ' . $tamu['nama_agenda']
                         : ($tamu['keperluan'] ?? '-');
            $sheet->setCellValue('H' . $rowIdx, $keperluan);
            $sheet->setCellValue('I' . $rowIdx, $tamu['nama_pegawai'] ?? 'Tamu Agenda');
            $sheet->setCellValue('J' . $rowIdx, date('d-m-Y H:i', strtotime($tamu['waktu_datang'])));
            $sheet->setCellValue('K' . $rowIdx, $tamu['waktu_pulang'] ? date('d-m-Y H:i', strtotime($tamu['waktu_pulang'])) : '-');
            $sheet->setCellValue('L' . $rowIdx, ucfirst($tamu['status_kunjungan']));

            // Alignments & Borders
            $cellRange = 'A' . $rowIdx . ':L' . $rowIdx;
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('A' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('D' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('F' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('K' . $rowIdx)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('L' . $rowIdx)->getAlignment()->setHorizontal('center');

            $rowIdx++;
        }

        // Auto column widths
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'laporan_kunjungan_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
}
