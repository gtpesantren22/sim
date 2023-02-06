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
            $data['dcair'] = $crr->jml;
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
        $jml = $this->db->query("SELECT SUM(nom_cair) as nom_cair, SUM(nom_serap) as nom_serap FROM real_sm WHERE kode_pengajuan = '$kd_pnj' ")->row();
        $dataReal = $this->model->getBy('real_sm', 'kode_pengajuan', $kd_pnj)->result();

        $lembaga =  $dataPj->lembaga;
        $tgl_cair = $this->input->post('tgl_cair', true);
        $kasir = $this->input->post('kasir', true);
        $penerima = $this->input->post('penerima', true);

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

            $this->model->input('realis', $dt);
            $this->model->delete('real_sm', 'id_realis', $id_pnj);
        }

        $psn = '
*INFORMASI PENCAIRAN PENGAJUAN*

Pencairan pengajuan dari :
    
Lembaga : ' . $lem . '
Kode Pengajuan : ' . $kd_pnj . '
Pada : ' . $tgl_cair . '
Nominal : ' . rupiah($jml->nom_serap) . '
Penerima : ' . $penerima . '

*_telah dicairkan oleh Bendahara Bag. Admin Pencairan._*
Terimakasih';

        if ($this->db->affected_rows() > 0) {
            // kirim_group($this->apiKey, '120363040973404347@g.us', $psn);
            // kirim_group($this->apiKey, '120363042148360147@g.us', $psn);
            // kirim_person($this->apiKey, '082264061060', $psn);
            kirim_person($this->apiKey, '085236924510', $psn);

            $this->session->set_flashdata('ok', 'Pengajuan sudah dicairkan');
            redirect('kasir/cairProses/' . $kd_pnj);
        } else {
            $this->session->set_flashdata('error', 'Pengajuan tidak bisa dicairkan');
            redirect('kasir/cairProses/' . $kd_pnj);
        }
    }

    public function pengajuanDisp()
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;
        $data['bulan'] = $this->bulan;

        $data['data'] = $this->model->getPengajuanDisp($this->tahun)->result();
        // $data['lembaga'] = $this->model->getBy2('lembaga', 'kode'$this->tahun)->result();
        // $data['pj'] = $this->model->getPjn('pengajuan', $this->lembaga, $this->tahun)->row();

        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/pengajuan', $data);
        $this->load->view('kasir/foot');
    }

    public function tanggungan()
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;
        $data['bulan'] = $this->bulan;

        $data['data'] = $this->model->getByJoin('tangg', 'tb_santri', 'nis', 'nis', 'tangg.tahun', $this->tahun)->result();

        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/tanggungan', $data);
        $this->load->view('kasir/foot');
    }

    public function delTanggungan($id)
    {
        $this->model->delete('tangg', 'id_tangg', $id);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Tanggungan berhasil dihapus');
            redirect('kasir/tanggungan');
        } else {
            $this->session->set_flashdata('error', 'Tanggungan berhasil dihapus');
            redirect('kasir/tanggungan');
        }
    }

    public function discrb($nis)
    {
        $data['user'] = $this->Auth_model->current_user();
        $data['tahun'] = $this->tahun;
        $data['bulan'] = $this->bulan;

        $data['sn'] = $this->model->getBy('tb_santri', 'nis', $nis)->row();
        $data['tgn'] = $this->model->getBy2('tangg', 'nis', $nis, 'tahun', $this->tahun)->row();
        $data['masuk'] = $this->db->query("SELECT SUM(nominal) AS jml FROM pembayaran WHERE nis = '$nis' AND tahun = '$this->tahun' GROUP BY nis ")->row();
        $data['bayar'] = $this->model->getBy2('pembayaran', 'nis', $nis, 'tahun', $this->tahun)->result();


        $this->load->view('kasir/head', $data);
        $this->load->view('kasir/discrb', $data);
        $this->load->view('kasir/foot');
    }

    public function addbayar()
    {
        $user = $this->Auth_model->current_user();

        $nominal = rmRp($this->input->post('nominal', true));
        $tgl = $this->input->post('tgl', true);
        $kasir = $user->nama;
        $nama = $this->input->post('nama', true);
        $nis = $this->input->post('nis', true);
        $tahun = $this->tahun;
        $dekos = $this->input->post('dekos', true);
        $bulan_bayar = $this->input->post('bulan', true);

        $dp = $this->model->getBy('tb_santri', 'nis', $nis)->row();
        $dpBr = $this->model->getBy2('tangg', 'nis', $nis, 'tahun', $this->tahun)->row();

        $by = $nominal + $this->input->post('masuk', true);
        $ttl = $this->input->post('ttl', true);
        $alm = $dp->desa . '-' . $dp->kec . '-' . $dp->kab;
        // $hpNo = $dp->hp;
        $hpNo = '085236924510';

        $data = [
            'nis' => $nis,
            'nama' => $nama,
            'tgl' => $tgl,
            'nominal' => $nominal,
            'bulan' => $bulan_bayar,
            'tahun' => $tahun,
            'kasir' => $kasir,
        ];
        $data2 = [
            'nis' => $nis,
            'nominal' => 300000,
            'bulan' => $bulan_bayar,
            'tahun' => $tahun,
            'tgl' => $tgl,
            'penerima' => $kasir,
            'stts' => 1,
            'waktu' => date('Y-m-d H:i'),
        ];

        $pesan = '
*KWITANSI PEMBAYARAN ELEKTRONIK*
*PP DARUL LUGHAH WAL KAROMAH*
Bendahara Pondok Pesantren Darul Lughah Wal Karomah telah menerima pembayaran BP dari wali santri berikut :
    
No. BRIVA : *' . $dpBr->briva . '*
Nama : *' . $nama . '*
Alamat : *' . $alm . '* 
Nominal Pembayaran: *' . rupiah($nominal) . '*
Tanggal Bayar : *' . $tgl . '*
Pembayaran Untuk: *BP (Biaya Pendidikan) bulan ' . $this->bulan[$bulan_bayar] . '*
Penerima: *' . $kasir . '*

Bukti Penerimaan ini *DISIMPAN* oleh wali santri sebagai bukti pembayaran Biaya Pendidikan PP Darul Lughah Wal Karomah Tahun Pelajaran ' . $tahun . '.
*Hal – hal yang berkaitan dengan Teknis keuangan dapat menghubungi Contact Person Bendahara berikut :*
*https://wa.me/6287757777273*
*https://wa.me/6285235583647*

Terimakasih';

        if ($by > $ttl) {
            $this->session->set_flashdata('error', 'Maaf pembayaran melebihi');
            redirect('kasir/discrb/' . $nis);
        } else {
            $cek = $this->db->query("SELECT * FROM pembayaran WHERE nis = '$nis' AND bulan = '$bulan_bayar' AND tahun = '$tahun' ")->num_rows();
            if ($cek < 1) {
                if ($dekos == 'Y') {
                    $this->model->inputDb2('kos', $data2);
                    $this->model->input('pembayaran', $data);

                    if ($this->db->affected_rows() > 0) {
                        kirim_person($this->api_key, $hpNo, $pesan);
                        $this->session->set_flashdata('ok', 'Tanggungan berhasil diinput');
                        redirect('kasir/discrb/' . $nis);
                    } else {
                        $this->session->set_flashdata('error', 'Tanggungan tidak berhasil diinput');
                        redirect('kasir/discrb/' . $nis);
                    }
                } else {
                    $this->model->input('pembayaran', $data);

                    if ($this->db->affected_rows() > 0) {
                        kirim_person($this->api_key, $hpNo, $pesan);
                        $this->session->set_flashdata('ok', 'Tanggungan berhasil diinput');
                        redirect('kasir/discrb/' . $nis);
                    } else {
                        $this->session->set_flashdata('error', 'Tanggungan tidak berhasil diinput');
                        redirect('kasir/discrb/' . $nis);
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Maaf pembayaran ini bulan ini sudah ada');
                redirect('kasir/discrb/' . $nis);
            }
        }
    }

    public function delBayar($id)
    {
        $data = $this->model->getBy('pembayaran', 'id', $id)->row();

        // $sql = mysqli_query($conn, "DELETE FROM pembayaran WHERE id = '$id' AND tahun = '$tahun_ajaran' ");
        // $sql2 = mysqli_query($conn_dekos, "DELETE FROM kos WHERE nis = '$nis' AND bulan = '$buln' AND tahun = '$tahun' ");

        $this->model->deleteBayar($data->nis, $data->bulan, $data->tahun);
        $this->model->delete('pembayaran', 'id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Tanggungan berhasil dihapus');
            redirect('kasir/discrb/' . $data->nis);
        } else {
            $this->session->set_flashdata('error', 'Tanggungan tidak berhasil dihapus');
            redirect('kasir/discrb/' . $data->nis);
        }
    }
}