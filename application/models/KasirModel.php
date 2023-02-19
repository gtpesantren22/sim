<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KasirModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('dekos', true);
        $this->db3 = $this->load->database('sekretaris', true);
        $this->db4 = $this->load->database('santri', true);
    }
    function apikey()
    {
        $this->db->select('*');
        $this->db->from('api');
        $this->db->where('nama', 'Bendahara');
        return $this->db->get();
    }

    function getBy($table, $where1, $dtwhere1)
    {
        $this->db->where($where1, $dtwhere1);
        return $this->db->get($table);
    }
    function getBy2($table, $where1, $dtwhere1, $where2, $dtwhere2)
    {
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }

    public function getPengajuan($tahun)
    {
        $this->db->from('pengajuan');
        $this->db->join('lembaga', 'ON pengajuan.lembaga=lembaga.kode');
        $this->db->where('lembaga.tahun', $tahun);
        $this->db->where('pengajuan.tahun', $tahun);
        $this->db->not_like('kode_pengajuan', 'DISP.');
        return $this->db->get();
    }

    public function getPengajuanDisp($tahun)
    {
        $this->db->from('pengajuan');
        $this->db->join('lembaga', 'ON pengajuan.lembaga=lembaga.kode');
        $this->db->where('lembaga.tahun', $tahun);
        $this->db->where('pengajuan.tahun', $tahun);
        $this->db->like('kode_pengajuan', 'DISP.');
        return $this->db->get();
    }

    function getBySum($table, $where, $dtwhere, $sum)
    {
        $this->db->select('*');
        $this->db->select_sum($sum, 'jml');
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }

    public function update($table, $data, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->update($table, $data);
    }

    public function delete($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->delete($table);
    }

    public function deleteBayar($nis, $bulan, $tahun)
    {
        $this->db2->where('nis', $nis);
        $this->db2->where('bulan', $bulan);
        $this->db2->where('tahun', $tahun);
        $this->db2->delete('kos');
    }

    public function input($tbl, $data)
    {
        $this->db->insert($tbl, $data);
    }
    public function inputDb2($tbl, $data)
    {
        $this->db2->insert($tbl, $data);
    }

    function getByJoin($table1, $table2, $on1, $on2, $where1, $dtwhere1)
    {
        $this->db->from($table1);
        $this->db->join($table2, 'ON ' . $table1 . '.' . $on1 . ' = ' . $table2 . '.' . $on2);
        $this->db->where($where1, $dtwhere1);
        return $this->db->get();
    }

    public function updateDb2($table, $data, $where, $dtwhere)
    {
        $this->db2->where($where, $dtwhere);
        $this->db2->update($table, $data);
    }

    public function updateDb3($table, $data, $where, $dtwhere)
    {
        $this->db3->where($where, $dtwhere);
        $this->db3->update($table, $data);
    }

    public function updateDb4($table, $data, $where, $dtwhere)
    {
        $this->db4->where($where, $dtwhere);
        $this->db4->update($table, $data);
    }

    public function getBayarAll()
    {
        $this->db->select('pembayaran.*, tb_santri.jkl, tb_santri.k_formal, tb_santri.t_formal');
        $this->db->from('pembayaran');
        $this->db->join('tb_santri', 'ON pembayaran.nis=tb_santri.nis');
        $this->db->where('pembayaran.tahun', $this->tahun);
        $this->db->order_by('pembayaran.tgl', 'DESC');
        return $this->db->get();
    }

    public function getMutasi()
    {
        // $sql = mysqli_query($conn_sekretaris, "SELECT a.*, b.* FROM mutasi a JOIN tb_santri b ON a.nis=b.nis WHERE status = 0 AND aktif = 'Y' ORDER BY id_mutasi DESC ");
        $this->db3->select('mutasi.*, tb_santri.*');
        $this->db3->from('mutasi');
        $this->db3->join('tb_santri', 'ON mutasi.nis=tb_santri.nis');
        $this->db3->where('mutasi.status', '0');
        $this->db3->where('tb_santri.aktif', 'Y');
        return $this->db3->get();
    }

    function getByDb3($table, $where1, $dtwhere1)
    {
        $this->db3->where($where1, $dtwhere1);
        return $this->db3->get($table);
    }

    function getBy2Db3($table, $where1, $dtwhere1, $where2, $dtwhere2)
    {
        $this->db3->where($where1, $dtwhere1);
        $this->db3->where($where2, $dtwhere2);
        return $this->db3->get($table);
    }

    public function getDekosSum($tahun)
    {
        $this->db2->select_sum('nominal', 'nominal');
        $this->db2->where('tahun', $tahun);
        $this->db2->from('setor');
        return $this->db2->get();
    }
}
