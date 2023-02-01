<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Lembaga extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('LembagaModel', 'model');
		$this->load->model('Auth_model');

		$user = $this->Auth_model->current_user();
		$this->tahun = $this->session->userdata('tahun');
		// $this->jenis = ['A. Belanja Barang', 'B. Langganan & Jasa', 'Belanja Kegiatan', 'D. Umum'];
		$this->bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juli', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

		$api = $this->model->apiKey()->row();
		$this->apiKey = $api->nama_key;
		$this->lembaga = $user->lembaga;

		if (!$this->Auth_model->current_user() || $user->level != 'lembaga') {
			redirect('login/logout');
		}
	}

	public function index()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['rab'] = $this->model->getBySum2('rab', 'lembaga', $this->lembaga, 'tahun', $this->tahun, 'total')->row();
		$data['realis'] = $this->model->getBySum2('realis', 'lembaga', $this->lembaga, 'tahun', $this->tahun, 'nominal')->row();
		$data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();
		$data['spj'] = $this->model->getPjn('spj', $this->lembaga, $this->tahun)->row();

		$sumA = $this->model->getTotalRabJenis('A', $this->lembaga, $this->tahun)->row();
		$sumB = $this->model->getTotalRabJenis('B', $this->lembaga, $this->tahun)->row();
		$sumC = $this->model->getTotalRabJenis('C', $this->lembaga, $this->tahun)->row();
		$sumD = $this->model->getTotalRabJenis('D', $this->lembaga, $this->tahun)->row();

		$pakaiA = $this->model->getTotalRealJenis('A', $this->lembaga, $this->tahun)->row();
		$pakaiB = $this->model->getTotalRealJenis('B', $this->lembaga, $this->tahun)->row();
		$pakaiC = $this->model->getTotalRealJenis('C', $this->lembaga, $this->tahun)->row();
		$pakaiD = $this->model->getTotalRealJenis('D', $this->lembaga, $this->tahun)->row();

		$data['prsnA'] = round($pakaiA->nominal / $sumA->total * 100, 1);
		$data['prsnB'] = round($pakaiB->nominal / $sumB->total * 100, 1);
		$data['prsnC'] = round($pakaiC->nominal / $sumC->total * 100, 1);
		$data['prsnD'] = round($pakaiD->nominal / $sumD->total * 100, 1);

		$data['info'] = $this->model->getBy('info', 'tahun', $this->tahun)->result();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/index', $data);
		$this->load->view('lembaga/foot', $data);
	}

	public function rab()
	{
		$kode = $this->lembaga;
		$data['data'] = $this->model->getBy2('rab', 'lembaga', $kode, 'tahun', $this->tahun)->result();
		$data['lembaga'] = $this->model->getBy('lembaga', 'kode', $kode)->row();

		$data['sumA'] = $this->model->getTotalRabJenis('A', $kode, $this->tahun)->row();
		$data['sumB'] = $this->model->getTotalRabJenis('B', $kode, $this->tahun)->row();
		$data['sumC'] = $this->model->getTotalRabJenis('C', $kode, $this->tahun)->row();
		$data['sumD'] = $this->model->getTotalRabJenis('D', $kode, $this->tahun)->row();

		$data['pakaiA'] = $this->model->getTotalRealJenis('A', $this->lembaga, $this->tahun)->row();
		$data['pakaiB'] = $this->model->getTotalRealJenis('B', $this->lembaga, $this->tahun)->row();
		$data['pakaiC'] = $this->model->getTotalRealJenis('C', $this->lembaga, $this->tahun)->row();
		$data['pakaiD'] = $this->model->getTotalRealJenis('D', $this->lembaga, $this->tahun)->row();

		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/rabDetail', $data);
		$this->load->view('lembaga/foot');
	}

	public function realis()
	{
		$lembaga = $this->lembaga;
		$data['data'] = $this->model->getBy('lembaga', 'tahun', $this->tahun)->result();
		$data['tahun'] = $this->tahun;
		$data['lembaga'] = $this->model->getBy('lembaga', 'kode', $lembaga)->row();

		$data['sumA'] = $this->model->getTotalRabJenis('A', $lembaga, $this->tahun)->row();
		$data['sumB'] = $this->model->getTotalRabJenis('B', $lembaga, $this->tahun)->row();
		$data['sumC'] = $this->model->getTotalRabJenis('C', $lembaga, $this->tahun)->row();
		$data['sumD'] = $this->model->getTotalRabJenis('D', $lembaga, $this->tahun)->row();

		$data['pakaiA'] = $this->model->getTotalRealJenis('A', $lembaga, $this->tahun)->row();
		$data['pakaiB'] = $this->model->getTotalRealJenis('B', $lembaga, $this->tahun)->row();
		$data['pakaiC'] = $this->model->getTotalRealJenis('C', $lembaga, $this->tahun)->row();
		$data['pakaiD'] = $this->model->getTotalRealJenis('D', $lembaga, $this->tahun)->row();

		$data['rabA'] = $this->model->getBy2('rab', 'lembaga', $lembaga, 'tahun', $this->tahun)->result();
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/realDetail', $data);
		$this->load->view('lembaga/foot');
	}

	public function cekRealis($kode)
	{
		$data['rab'] = $this->model->getBy('rab', 'kode', $kode)->row();
		$data['lem'] = $this->model->getBy2('lembaga', 'kode', $data['rab']->lembaga, 'tahun', $this->tahun)->row();
		$data['rel'] = $this->model->getBySum('realis', 'kode', $kode, 'nominal')->row();
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['relData'] = $this->model->getBy('realis', 'kode', $kode)->result();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/cekRab', $data);
		$this->load->view('lembaga/foot');
	}

	public function pengajuan()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getPengajuan($this->lembaga, $this->tahun)->result();
		$data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/pengajuan', $data);
		$this->load->view('lembaga/foot');
	}

	public function pengajuanAdd()
	{
		$tahun = $this->tahun;
		$lembaga = $this->lembaga;
		$jenis = $this->input->post('jenis', true);

		$pj = $this->db->query("SELECT MAX(no_urut) as nu FROM pengajuan WHERE tahun = '$tahun'")->row();
		$urut = $pj->nu + 1;

		$tahunInput = $this->input->post('tahun', true);
		$bln = $this->input->post('bulan', true);

		$dataKode = $this->db->query("SELECT COUNT(*) as jml FROM pengajuan WHERE lembaga = '$lembaga' AND tahun = '$tahun' ")->row();
		$kodeBarang = $dataKode->jml + 1;
		$noUrut = (int) substr($kodeBarang, 0, 3);
		if ($jenis === 'disposisi') {
			$kodeBarang = sprintf("%03s", $noUrut) . '.DISP.' . $lembaga . '.' . date('dd') . '.' . $bln . '.' . $tahunInput;
			$rdrc = 'lembaga/disposisi';
		} else {
			$kodeBarang = sprintf("%03s", $noUrut) . '.' . $lembaga . '.' . date('dd') . '.' . $bln . '.' . $tahunInput;
			$rdrc = 'lembaga/pengajuan';
		}
		$kd_pjn = htmlspecialchars($kodeBarang);

		$data = [
			'id_pn' => $this->uuid->v4(),
			'kode_pengajuan' => $kd_pjn,
			'lembaga' => $lembaga,
			'bulan' => $this->input->post('bulan', true),
			'tahun' => $this->tahun,
			'no_urut' => $urut,
			'at' => date('Y-m-d H:i'),
			'stts' => 'no'
		];
		$data2 = [
			'id_spj' => $this->uuid->v4(),
			'kode_pengajuan' => $kd_pjn,
			'lembaga' => $lembaga,
			'bulan' => $this->input->post('bulan', true),
			'tahun' => $this->tahun,
			'no_urut' => $urut
		];

		$cek = $this->db->query("SELECT * FROM pengajuan WHERE kode_pengajuan = '$kd_pjn' AND tahun = '$tahun' ")->num_rows();
		if ($cek < 1) {
			$this->model->input('pengajuan', $data);
			$this->model->input('spj', $data2);

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Pengajuan baru berhasil dibuat');
				redirect($rdrc);
			} else {
				$this->session->set_flashdata('error', 'Pengajuan baru tidak bisa');
				redirect($rdrc);
			}
		} else {
			$this->session->set_flashdata('error', 'Maaf. Sudah ada pengajuan hari ini');
			redirect($rdrc);
		}
	}

	public function pengajuanDetail($kode)
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getPengajuan($this->lembaga, $this->tahun)->result();
		$data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();
		$data['bidang'] = $this->model->getBy('bidang', 'tahun', $this->tahun)->result();

		$this->load->view('lembaga/head', $data);
		if (preg_match("/DISP./i", $kode)) {
			$this->load->view('lembaga/pengajuanDetailDisp', $data);
		} else {
			$this->load->view('lembaga/pengajuanDetail', $data);
		}
		$this->load->view('lembaga/foot');
	}


	public function spj()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getSpj($this->lembaga, $this->tahun)->result();
		$data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/spj', $data);
		$this->load->view('lembaga/foot');
	}

	public function show()
	{
		$kol = $this->lembaga;
		$tahun = $this->tahun;

		$data['query'] = $this->db->query("SELECT * FROM rab WHERE lembaga = '$kol' AND tahun = '$tahun' GROUP BY jenis")->result();

		$this->load->view('lembaga/show', $data);
	}

	function data()
	{
		$data['kotaId'] = $this->input->post('kotaId', true);
		$data['kol'] = $this->lembaga;
		$data['tahun'] = $this->tahun;
		$this->load->view('lembaga/data', $data);
	}

	function cari()
	{
		$data['judul'] = $this->input->get('judul', true);
		$data['kol'] = $this->lembaga;
		$data['tahun'] = $this->tahun;
		$this->load->view('lembaga/cari', $data);
	}

	public function addItem()
	{
		$id = $this->uuid->v4();
		$kode = $this->input->post('id_rab');
		$bulan = $this->input->post('bln_pj');
		// $l = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rab WHERE kode = '$kode' AND tahun = '$tahun_ajaran' "));
		$l = $this->model->getBy2('rab', 'kode', $kode, 'tahun', $this->tahun)->row();
		$kd_rab = $l->kode;
		$lembaga = $l->lembaga;
		$bidang = $l->bidang;
		$jenis = $l->jenis;
		$tgl = $this->input->post('tgl', true);
		$qty = $this->input->post('qty', true);
		$pj = $this->input->post('pj', true);
		$tahun = $this->tahun;
		$nominal = $l->harga_satuan * $qty;
		$nm_rab =  $l->nama;
		$ket = $nm_rab . ' - @ ' . $qty . ' x ' . number_format($l->harga_satuan, 0, ',', '.');
		$kd_pjn = $this->input->post('kode_pengajuan', true);
		$sisa_jml = $this->input->post('sisa_jml', true);

		if ($jenis === 'A') {
			$stas = 'barang';
		} else {
			$stas = 'tunai';
		}

		if ($qty > $sisa_jml) {
			$this->session->set_flashdata('error', 'Maaf. Jumlah pengajuan anda melebihi dari yang tersisa');
			redirect('lembaga/pengajuanDetail/' . $kd_pjn);
		} elseif ($qty < 1) {
			$this->session->set_flashdata('error', 'Jumlah item 0. Jumlah item harus diisi');
			redirect('lembaga/pengajuanDetail/' . $kd_pjn);
		} else {

			$data = [
				'id_realis' => $id,
				'lembaga' => $lembaga,
				'bidang' => $bidang,
				'jenis' => $jenis,
				'kode' => $kd_rab,
				'vol' => $qty,
				'nominal' => $nominal,
				'tgl' => $tgl,
				'pj' => $pj,
				'bulan' => $bulan,
				'tahun' => $tahun,
				'ket' => $ket,
				'kode_pengajuan' => $kd_pjn,
				'nom_cair' => $nominal,
				'nom_serap' => $nominal,
				'stas' => $stas
			];

			$this->model->input('real_sm', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Item pengajuan berhasil ditambahkan');
				redirect('lembaga/pengajuanDetail/' . $kd_pjn);
			} else {
				$this->session->set_flashdata('error', 'Item pengajuan tidak ditambahkan');
				redirect('lembaga/pengajuanDetail/' . $kd_pjn);
			}
		}
	}

	public function uploadHonor()
	{
		$kd_rab = $this->input->post('kd_rab', true);
		$kd_ppnj = $this->input->post('kd_ppnj', true);
		$ktb = $this->input->post('ktb', true);

		$file = $this->model->getBy2('honor_file', 'kode_pengajuan', $kd_ppnj, 'kode_rab', $kd_rab)->row();

		$file_name = 'HNR-' . rand(0, 99999999);
		$config['upload_path']          = FCPATH . '/vertical/assets/uploads/honor/';
		$config['allowed_types']        = 'xls|xlsx';
		$config['file_name']            = $file_name;
		$config['overwrite']            = true;
		$config['max_size']             = 1024; // 1MB
		$config['max_width']            = 1080;
		$config['max_height']           = 1080;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('f_rin')) {
			$data['error'] = $this->upload->display_errors();
		} else {
			$uploaded_data = $this->upload->data();

			if ($ktb === 'baru') {
				$new_data = [
					'kode_pengajuan' => $kd_ppnj,
					'kode_rab' => $kd_rab,
					'tgl_upload' => date('Y-m-d H:i'),
					'files' =>  $uploaded_data['file_name'],
					'tahun' => $this->tahun,
				];
				$this->model->input('honor_file', $new_data);
			} elseif ($ktb === 'update') {
				$kode_pengajuan = $kd_ppnj;
				$kode_rab = $kd_rab;
				$new_data = [
					'files' =>  $uploaded_data['file_name']
				];
				$this->model->update2('honor_file', $new_data, 'kode_pengajuan', $kode_pengajuan, 'kode_rab', $kode_rab);
				unlink('./vertical/assets/uploads/honor/' . $file->files);
			}

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Bukti file berhasil diupload');
				redirect('lembaga/pengajuanDetail/' . $kd_ppnj);
			} else {
				$this->session->set_flashdata('error', 'Bukti file gagal diupload');
				redirect('lembaga/pengajuanDetail/' . $kd_ppnj);
			}
		}
	}

	public function delReal($kode)
	{
		$dtd = $this->model->getBy('real_sm', 'id_realis', $kode)->row();
		$this->model->delete('real_sm', 'id_realis', $kode);
		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('ok', 'Realisasi berhasil dihapus');
			redirect('lembaga/pengajuanDetail/' . $dtd->kode_pengajuan);
		} else {
			$this->session->set_flashdata('error', 'Realisasi gagal dihapus');
			redirect('lembaga/pengajuanDetail/' . $dtd->kode_pengajuan);
		}
	}

	public function ajukan($kode)
	{
		$bulan = $this->bulan;
		$data = [
			'stts' => 'yes'
		];

		$dt = $this->model->getBy('pengajuan', 'kode_pengajuan', $kode)->row();
		$lm = $this->model->getBy2('lembaga', 'kode', $dt->lembaga, 'tahun', $this->tahun)->row();
		$jml = $this->model->getBySum('real_sm', 'kode_pengajuan', $dt->kode_pengajuan, 'nominal')->row();

		$perod = $bulan[$dt->bulan] . ' ' . $dt->tahun;
		// $ww = date('d-M-Y H:i:s');

		if (preg_match("/DISP./i", $dt->kode_pengajuan)) {
			$rt = '*(DISPOSISI)*';
		} else {
			$rt = '';
		}

		$psn = '
*INFORMASI PERMOHONAN VERIFIKASI* ' . $rt . '

Ada pengajuan baru dari :
    
Lembaga : ' . $lm->nama . '
Kode Pengajuan : ' . $dt->kode_pengajuan . '
Periode : ' . $perod . '
Pada : ' . $dt->at . '
Nominal : ' . rupiah($jml->jm) . '

*_dimohon kepada SUB BAG ACCOUNTING untuk segera mengecek nya di https://simkupaduka.ppdwk.com/_*
Terimakasih';

		$this->model->update('pengajuan', $data, 'kode_pengajuan', $kode);
		if ($this->db->affected_rows() > 0) {

			// kirim_group($this->apiKey, '120363040973404347@g.us', $psn);
			// kirim_group($this->apiKey, '120363042148360147@g.us', $psn);
			// kirim_person($this->apiKey, '082302301003', $psn);
			kirim_person($this->apiKey, '085236924510', $psn);

			$this->session->set_flashdata('ok', 'Pengajuan berhasil dilanjutkan ke Accounting');
			redirect('lembaga/pengajuanDetail/' . $kode);
		} else {
			$this->session->set_flashdata('error', 'Pengajuan gagal dilanjutkan ke Accounting');
			redirect('lembaga/pengajuanDetail/' . $kode);
		}
	}

	public function uploadSpj()
	{
		$id = $this->uuid->v4();
		$kode  = $this->input->post('kode');
		$bulan  = $this->input->post('bulan');
		$tahun  = $this->input->post('tahun');
		$date = date('Y-m-d');
		$at = date('Y-m-d H:i:s');

		$file = $this->model->getBy('pengajuan', 'kode_pengajuan', $kode)->row();
		$lembaga = $this->model->getBy2('lembaga', 'kode', $file->lembaga, 'tahun', $this->tahun)->row();
		$spj = $this->model->getBy('spj', 'kode_pengajuan', $kode)->row();

		if (preg_match("/DISP./i", $kode)) {
			$rt = "*(DISPOSISI)*";
		} else {
			$rt = '';
		}

		$psn = '
*INFORMASI VERIFIKASI SPJ* ' . $rt . '

Ada pelaporan SPJ dari :
    
Lembaga : ' . $lembaga->nama . '
Kode Pengajuan : ' . $kode . '
Pada : ' . $at . '

*_dimohon kepada ACCOUNTING untuk segera mengecek nya di https://simkupaduka.ppdwk.com/_*
Terimakasih';

		$file_name = 'SPJ-' . rand(0, 99999999);
		$config['upload_path']          = FCPATH . '/vertical/assets/uploads/';
		$config['allowed_types']        = 'doc|docx|xls|xlsx|pdf';
		$config['file_name']            = $file_name;
		$config['overwrite']            = true;
		$config['max_size']             = 5120; // 1MB
		$config['max_width']            = 1080;
		$config['max_height']           = 1080;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('f_rin')) {
			$data['error'] = $this->upload->display_errors();
		} else {
			$uploaded_data = $this->upload->data();

			$data = [
				'stts' => 1,
				'file_spj' => $uploaded_data['file_name'],
				'tgl_upload' => $date,
			];
			$data2 = [
				'spj' => 1,
			];


			if ($spj->file_spj != '') {
				$this->model->update('spj', $data, 'kode_pengajuan', $kode);
				unlink('./vertical/assets/uploads/' . $spj->file_spj);
			} else {
				$this->model->update('spj', $data, 'kode_pengajuan', $kode);
				$this->model->update('pengajuan', $data2, 'kode_pengajuan', $kode);
			}


			if ($this->db->affected_rows() > 0) {

				// kirim_group($$this->apiKey, '120363040973404347@g.us', $psn);
				// kirim_group($$this->apiKey, '120363042148360147@g.us', $psn);
				kirim_person($this->apiKey, '085236924510', $psn);

				$this->session->set_flashdata('ok', 'Bukti SPJ berhasil diupload');
				redirect('lembaga/spj');
			} else {
				$this->session->set_flashdata('error', 'Bukti SPJ gagal diupload');
				redirect('lembaga/spj');
			}
		}
	}

	public function disposisi()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getPengajuan2($this->lembaga, $this->tahun)->result();
		$data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();
		$data['hak_aks'] = $this->model->getBy2('akses', 'lembaga', $this->lembaga, 'tahun', $this->tahun)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/pengajuanDisp', $data);
		$this->load->view('lembaga/foot');
	}

	public function addDisp()
	{
		$kd_pjn = $this->input->post('kode_pengajuan', true);

		$id = $this->uuid->v4();
		$lembaga = $this->input->post('lembaga', true);
		$bidang = $this->input->post('bidang', true);
		$jenis = $this->input->post('jenis', true);
		$harga = rmRp($this->input->post('harga', true));
		$qty = $this->input->post('qty', true);
		$nominal = $harga * $qty;
		$satuan = $this->input->post('satuan', true);
		$tgl = $this->input->post('tgl', true);
		$pj = $this->input->post('pj', true);
		$bulan = $this->input->post('bln_pj', true);
		$tahun = $this->input->post('tahun', true);
		// $ket = $this->input->post('ket', true);
		$nm_rab = $this->input->post('nm_rab', true);

		$ket = $nm_rab . ' - @ ' . $qty . ' ' . $satuan . ' x ' . number_format($harga, 0, ',', '.');
		// $sisa_jml = $this->input->post('sisa_jml', true);

		if ($jenis === 'A') {
			$stas = 'barang';
		} else {
			$stas = 'tunai';
		}

		$jml = $this->db->query("SELECT SUM(nom_cair) AS jml FROM real_sm WHERE kode_pengajuan LIKE 'DISP.%' AND tahun = '$tahun' ")->row();
		$jml2 = $this->db->query("SELECT SUM(nom_cair) AS jml FROM realis WHERE kode_pengajuan LIKE 'DISP.%' AND tahun = '$tahun' ")->row();
		$kfe = 50000000 - ($jml->jml + $jml2->jml);

		$data = [
			'id_realis' => $id,
			'lembaga' => $lembaga,
			'bidang' => $bidang,
			'jenis' => $jenis,
			'kode' => 'Disposisi',
			'vol' => $qty,
			'nominal' => $nominal,
			'tgl' => $tgl,
			'pj' => $pj,
			'bulan' => $bulan,
			'tahun' => $tahun,
			'ket' => $ket,
			'kode_pengajuan' => $kd_pjn,
			'nom_cair' => $nominal,
			'nom_serap' => $nominal,
			'stas' => $stas
		];


		if ($kfe >= $nominal) {
			$this->model->input('real_sm', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Item pengajuan berhasil ditambahkan');
				redirect('lembaga/pengajuanDetail/' . $kd_pjn);
			} else {
				$this->session->set_flashdata('error', 'Item pengajuan tidak ditambahkan');
				redirect('lembaga/pengajuanDetail/' . $kd_pjn);
			}
		} else {
			$this->session->set_flashdata('error', 'Maaf, Sisa dana disposisi tidak mencukupi');
			redirect('lembaga/pengajuanDetail/' . $kd_pjn);
		}
	}

	public function pak()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getBy2('pak', 'lembaga', $this->lembaga, 'tahun', $this->tahun)->result();
		$data['tgl'] = $this->model->getBy('akses', 'lembaga', 'umum')->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();
		$data['hak_aks'] = $this->model->getBy2('akses', 'lembaga', $this->lembaga, 'tahun', $this->tahun)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/pak', $data);
		$this->load->view('lembaga/foot');
	}

	public function daftarPak()
	{
		$kode = 'PAK.' . $this->lembaga . '.' . rand(0, 99999);
		$lembaga = $this->lembaga;
		$tgl_pak = $this->input->post('tgl_pak');
		$tahun = $this->input->post('tahun');

		$data = [
			'kode_pak' => $kode,
			'lembaga' => $lembaga,
			'tgl_pak' => $tgl_pak,
			'status' => 'belum',
			'tahun' => $tahun,
		];

		$this->model->input('pak', $data);
		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('ok', 'pengajuan PAK berhasil ditambahkan');
			redirect('lembaga/pak');
		} else {
			$this->session->set_flashdata('error', 'pengajuan PAK tidak ditambahkan');
			redirect('lembaga/pak');
		}
	}

	public function pakDetail($kode)
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$data['data'] = $this->model->getBy('pak', 'kode_pak', $kode)->row();
		$data['rab'] = $this->model->getBy2('rab', 'lembaga', $this->lembaga, 'tahun', $this->tahun)->result();
		$data['rabTotal'] = $this->model->getBySum2('rab', 'lembaga', $this->lembaga, 'tahun', $this->tahun, 'total')->row();
		$data['realTotal'] = $this->model->getBySum2('realis', 'lembaga', $this->lembaga, 'tahun', $this->tahun, 'nominal')->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();
		$data['bidang'] = $this->model->getBy('bidang', 'tahun', $this->tahun)->result();

		// $data['tgl'] = $this->model->getBy('akses', 'lembaga', 'umum')->row();
		// $data['hak_aks'] = $this->model->getBy2('akses', 'lembaga', $this->lembaga, 'tahun', $this->tahun)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/pakDetail', $data);
		$this->load->view('lembaga/foot');
	}

	public function addDelPak()
	{
		$kd_pak = $this->uri->segment(3);
		$id_rab = $this->uri->segment(4);

		$rab = $this->model->getBy('rab', 'id_rab', $id_rab)->row();

		$qty = $rab->qty;
		$satuan = $rab->satuan;
		$harga_satuan = $rab->harga_satuan;
		$total = $rab->harga_satuan * $rab->qty;
		$ket = 'hapus';
		$tahun = $rab->tahun;

		$cek = $this->db->query("SELECT * FROM pak_detail WHERE kode_rab = '$rab->kode' ")->num_rows();

		if ($cek > 0) {
			$this->session->set_flashdata('error', 'Maaf. item RAB ini sudah dipakai PAK');
			redirect('lembaga/pakDetail/' . $kd_pak);
		} else {
			$data = [
				'kode_pak' => $kd_pak,
				'kode_rab' => $rab->kode,
				'qty' => $qty,
				'satuan' => $satuan,
				'harga_satuan' => $harga_satuan,
				'total' => $total,
				'ket' => $ket,
				'tahun' => $tahun,
				'snc' => 'belum',
			];

			$this->model->input('pak_detail', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Item berhasil ditambahkan');
				redirect('lembaga/pakDetail/' . $kd_pak);
			} else {
				$this->session->set_flashdata('error', 'Item tidak ditambahkan');
				redirect('lembaga/pakDetail/' . $kd_pak);
			}
		}
	}

	public function delPakDetail()
	{
		$kd_pak = $this->uri->segment(3);
		$kd_rab = $this->uri->segment(4);

		$this->model->delete('pak_detail', 'kode_rab', $kd_rab);
		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('ok', 'Item berhasil dihapus');
			redirect('lembaga/pakDetail/' . $kd_pak);
		} else {
			$this->session->set_flashdata('error', 'Item tidak dihapus');
			redirect('lembaga/pakDetail/' . $kd_pak);
		}
	}

	public function pakDetailEdit()
	{
		$data['user'] = $this->Auth_model->current_user();
		$data['tahun'] = $this->tahun;
		$data['bulan'] = $this->bulan;

		$kd_pak = $this->uri->segment(3);
		$id_rab = $this->uri->segment(4);

		$data['rab'] = $this->model->getBy('rab', 'id_rab', $id_rab)->row();
		$data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $this->lembaga, 'tahun', $this->tahun)->row();
		$data['rel'] = $this->model->getBySum('realis', 'kode', $data['rab']->kode, 'nominal')->row();
		$data['relJml'] = $this->model->getBySum('realis', 'kode', $data['rab']->kode, 'vol')->row();
		$data['pak'] = $this->model->getBy('pak', 'kode_pak', $kd_pak)->row();

		$this->load->view('lembaga/head', $data);
		$this->load->view('lembaga/pakDetailEdit', $data);
		$this->load->view('lembaga/foot');
	}

	public function addEditPak()
	{
		$kd_pak = $this->input->post('kd_pak', true);
		$kd_rab = $this->input->post('kd_rab', true);

		$rab = $this->model->getBy('rab', 'kode', $kd_rab)->row();

		$qty = $this->input->post('jml', true);
		$sisa = $this->input->post('sisa', true);

		$satuan = $rab->satuan;
		$harga_satuan = $rab->harga_satuan;
		$total = $rab->harga_satuan * $rab->qty;
		$ket = 'edit';
		$tahun = $rab->tahun;

		$cek = $this->db->query("SELECT * FROM pak_detail WHERE kode_rab = '$rab->kode' ")->num_rows();

		if ($cek > 0) {
			$this->session->set_flashdata('error', 'Maaf. item RAB ini sudah dipakai PAK');
			redirect('lembaga/pakDetail/' . $kd_pak);
		} elseif ($qty > $sisa) {
			$this->session->set_flashdata('error', 'Maaf. Jumlah QTY melebihi sisa');
			redirect('lembaga/pakDetailEdit/' . $kd_pak . '/' . $rab->id_rab);
		} else {
			$data = [
				'kode_pak' => $kd_pak,
				'kode_rab' => $rab->kode,
				'qty' => $qty,
				'satuan' => $satuan,
				'harga_satuan' => $harga_satuan,
				'total' => $total,
				'ket' => $ket,
				'tahun' => $tahun,
				'snc' => 'belum',
			];

			$this->model->input('pak_detail', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Item berhasil ditambahkan');
				redirect('lembaga/pakDetail/' . $kd_pak);
			} else {
				$this->session->set_flashdata('error', 'Item tidak ditambahkan');
				redirect('lembaga/pakDetail/' . $kd_pak);
			}
		}
	}

	public function addRab()
	{
		$kode_pak = $this->input->post('kode_pak',true);

		$dt_rab = $this->db->query("SELECT SUM(total) AS tt FROM rab_sm WHERE lembaga = '$this->lembaga' AND tahun = '$this->tahun' ")->row();
		$dt_pak = $this->db->query("SELECT SUM(total) AS tt FROM pak_detail WHERE kode_pak = '$kode_pak' AND tahun = '$this->tahun' ")->row();
		$total = $this->input->post('qty') * rmRp($this->input->post('harga_satuan', true));

		$data = [
			'id' => $this->uuid->v4(),
			'lembaga' => $this->lembaga,
			'jenis' => $this->input->post('jenis'),
			'bidang' => $this->input->post('bidang'),
			'kode' => $lembaga . '.' . $bidang .  '.' . $jenis . '.' . rand(),
			'nama' => $this->input->post('nama'),
			'rencana' => $this->input->post('rencana'),
			'qty' => $this->input->post('qty'),
			'satuan' => $this->input->post('satuan'),
			'total' => $total,
			'harga_satuan' => rmRp($this->input->post('harga_satuan', true)),
			'tahun' => $this->input->post('tahun'),
			'at' => date('Y-m-d H:i'),
			'kode_pak' => $kode_pak,
			'snc' => 'belum',
		];

		$pak = $dt_pak->tt;
		$rb = $dt_rab->tt;
		$ttl = $rb + $total;

		if ($pak >= $ttl) {
			$this->model->input('rab_sm', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('ok', 'Item berhasil ditambahkan');
				redirect('lembaga/pakDetail/' . $kode_pak);
			} else {
				$this->session->set_flashdata('error', 'Item tidak ditambahkan');
				redirect('lembaga/pakDetail/' . $kode_pak);
			}
		}
		
	}
}