<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasir extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('KasirModel', 'model');
        $this->load->model('Auth_model');

        $user = $this->Auth_model->current_user();
        $this->tahun = $this->session->userdata('tahun');
        // $this->jenis = ['A. Belanja Barang', 'B. Langganan & Jasa', 'Belanja Kegiatan', 'D. Umum'];
        $this->bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juli', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $api = $this->model->apiKey()->row();
        $this->apiKey = $api->nama_key;
        $this->lembaga = $user->lembaga;

        if (!$this->Auth_model->current_user() || $user->level != 'kasir') {
            redirect('login/logout');
        }
    }

    public function index()
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;

        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/index', $data);
        $this->load->view('kasir/foot');
    }

    public function pengajuan()
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;
        $data['bulan'] = $this->bulan;

        $data['data'] = $this->model->getPengajuan($this->tahun)->result();
        // $data['lembaga'] = $this->model->getBy2('lembaga', 'kode'$this->tahun)->result();
        // $data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();

        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/pengajuan', $data);
        $this->load->view('kasir/foot');
    }

    public function cairProses($kode)
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;
        $data['bulan'] = $this->bulan;

        $data['pjn'] = $this->model->getBy('pengajuan', 'kode_pengajuan', $kode)->row();
        $data['lembaga'] = $this->model->getBy2('lembaga', 'kode', $data['pjn']->lembaga, 'tahun', $this->tahun)->row();


        $crr = $this->model->getBySum('pencairan', 'kode_pengajuan', $kode, 'nominal_cair')->row();
        $dt2 = $this->model->getBySum('real_sm', 'kode_pengajuan', $kode, 'nominal')->row();

        if ($crr->jml > 0) {
            $data['tbl_slct'] = 'realis';
            $sts_tmbl = 'disabled';
            $data['dcair'] = $data['crr']->jml;
            $data['dblm'] = 0;
        } else {
            $data['tbl_slct'] = 'real_sm';
            $sts_tmbl = '';
            $data['dcair'] = 0;
            $data['dblm'] = $dt2->jml;
        }
        // $rls = mysqli_query($conn, "SELECT * FROM $tbl_slct WHERE kode_pengajuan = '$kode' AND stas = 'tunai' AND tahun = '$tahun_ajaran' ORDER BY stas ASC ");
        $data['rls'] = $this->model->getBy2($data['tbl_slct'], 'kode_pengajuan', $kode, 'stas', 'tunai')->result();
        $data['rls2'] = $this->model->getBy2($data['tbl_slct'], 'kode_pengajuan', $kode, 'stas', 'barang')->result();

        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/cair', $data);
        $this->load->view('kasir/foot');
    }

    public function editSerap()
    {
        $id = $this->input->post('id', true);
        $kode_pengajuan = $this->input->post('kode_pengajuan', true);
        $serap = rmRp($this->input->post('serap', true));
        $nom_cair = rmRp($this->input->post('nom_cair', true));
        $table = $this->input->post('table', true);

        if ($serap > $nom_cair) {
            $this->session->set_flashdata('error', 'Maaf. Nominal terserapnyanya lebih dari disetujui');
            redirect('kasir/cairProses/' . $kode_pengajuan);
        } else {
            $this->model->update($table, ['nom_serap' => $serap], 'id_realis', $id);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Nominal serap berhasil diperbarui');
                redirect('kasir/cairProses/' . $kode_pengajuan);
            } else {
                $this->session->set_flashdata('error', 'Nominal serap tidak berhasil diperbarui');
                redirect('kasir/cairProses/' . $kode_pengajuan);
            }
        }
    }

    public function cairkan()
    {

        $id = $this->uuid->v4();

        $kd_pnj = $this->input->post('kode_pengajuan', true);
        $dataPj = $this->model->getBy('pengajuan', 'kode_pengajuan', $kd_pnj)->row();
        $jml = $this->db->query("SELECT SUM(nom_cair), SUM(nom_serap) FROM real_sm WHERE kode_pengajuan = '$kd_pnj' ")->row();
        $dataReal = $this->model->getBy('real_sm', 'kode_pengajuan', $kd_pnj)->result();

        $lembaga =  $dataPj->lembaga;
        $tgl_cair = $this->model->input('tgl_cair', true);
        $kasir = $this->model->input('kasir', true);
        $penerima = $this->model->input('penerima', true);

        $data = [
            'id_cair' => $id,
            'kode_pengajuan' => $kd_pnj,
            'lembaga' => $lembaga,
            'nominal' => $jml->nom_cair,
            'nominal_cair' => $jml->nom_serap,
            'tgl_cair' => $tgl_cair,
            'kasir' => $kasir,
            'penerima' => $penerima,
            'tahun' => $this->tahun,
        ];
        $data2 = ['cair' => 1];
        $this->model->input('pencairan', $data);
        $this->model->update('pengajuan', $data2, 'kode_pengajuan', $kd_pnj);
        // $sql = mysqli_query($conn, "INSERT INTO pencairan VALUES ('$id', '$kd_pnj','$lembaga','$nominal','$nominal_cair', '$tgl_cair','$kasir', '$penerima', '$tahun_ajaran')");
        // $pnj = mysqli_query($conn, "UPDATE pengajuan SET cair = 1 WHERE kode_pengajuan = '$kd_pnj' AND tahun = '$tahun_ajaran' ");

        foreach ($dataReal as $x) {
            $id_pnj = $x->id_realis;
            $dt = [
                'id_realis' => $id_pnj,
                'lembaga' => $x->lembaga,
                'bidang' => $x->bidang,
                'jenis' => $x->jenis,
                'kode' => $x->kode,
                'vol' => $x->vol,
                'nominal' => $x->nominal,
                'tgl' => $x->tgl,
                'pj' => $x->pj,
                'bulan' => $x->bulan,
                'tahun' => $x->tahun,
                'ket' => $x->ket,
                'kode_pengajuan' => $x->kode_pengajuan,
                'nom_cair' => $x->nom_cair,
                'nom_serap' => $x->nom_serap,
                'stas' => $x->stas
            ];
            // $add = mysqli_query($conn, "INSERT INTO realis SELECT * FROM real_sm WHERE id_realis = '$id_pnj' AND tahun = '$tahun_ajaran' ");
            // $del = mysqli_query($conn, "DELETE FROM real_sm WHERE id_realis = '$id_pnj' AND tahun = '$tahun_ajaran' ");

            $this->model->input('realis', $dt);
            $this->model->delete('real_sm', 'id_realis', $id_pnj);
        }
    }
}
