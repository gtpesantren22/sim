<?php
if ($crr->jml > 0) {
    $tbl_slct = 'realis';
    $sts_tmbl = 'disabled';
    $dcair = $crr->jml;
    $dblm = 0;
} else {
    $tbl_slct = 'real_sm';
    $sts_tmbl = '';
    $dcair = 0;
    $dblm = $dt2->jml;
}

$dt = $this->db->query("SELECT SUM(nom_cair) as jml, SUM(IF( stas = 'tunai', nom_cair, 0)) AS tunai, SUM(IF( stas = 'barang', nom_cair, 0)) AS brg, SUM(IF( stas = 'tunai', nominal, 0)) AS tunai_asal, SUM(IF( stas = 'barang', nominal, 0)) AS brg_asal, SUM(IF( stas = 'tunai', nom_serap, 0)) AS tunai_serap, SUM(IF( stas = 'barang', nom_serap, 0)) AS brg_serap FROM $tbl_slct WHERE kode_pengajuan = '$pjn->kode_pengajuan' AND tahun = '$tahun' ")->row();
?>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Data Pengajuan Lembaga</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-wallet"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Realisasi</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th>Lembaga</th>
                                            <th>: <?= $lembaga->nama ?></th>
                                        </tr>
                                        <tr>
                                            <th>Periode</th>
                                            <th>: <?= $bulan[$pjn->bulan] . ' ' . $pjn->tahun ?></th>
                                        </tr>
                                        <tr>
                                            <th>Kode</th>
                                            <th>: <?= $pjn->kode_pengajuan ?></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p style="color: green; font-weight: bold; font-size: 15px;"><i class="fa fa-check"></i>
                                    Dana Cair</p>
                                <p style="color: green; font-weight: bold; font-size: 25px;"><?= rupiah($dcair) ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p style="color: red; font-weight: bold; font-size: 15px;"><i class="fa fa-times"></i>
                                    Belum Cair</p>
                                <p style="color: red; font-weight: bold; font-size: 25px;"><?= rupiah($dblm) ?>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode RAB</th>
                                        <th>PJ</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                        <th>Disetujui</th>
                                        <th>Terserap</th>
                                        <th>Ket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rls = mysqli_query($conn, "SELECT * FROM $tbl_slct WHERE kode_pengajuan = '$kode' AND stas = 'tunai' AND tahun = '$tahun_ajaran' ORDER BY stas ASC ");
                                    while ($ls_jns = mysqli_fetch_assoc($rls)) {
                                        $ids = explode('-', $ls_jns['id_realis']);
                                        $idOk = $ids[0];
                                    ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td><?= $ls_jns['kode']; ?></td>
                                            <td><?= $ls_jns['pj']; ?></td>
                                            <td><?= $ls_jns['ket']; ?></td>
                                            <td><?= rupiah($ls_jns['nominal']); ?></td>
                                            <td><?= rupiah($ls_jns['nom_cair']); ?></td>
                                            <td>
                                                <form action="" method="post">
                                                    <input type="hidden" name="id" value="<?= $ls_jns['id_realis']; ?>">
                                                    <div class="col-md-10">
                                                        <input type="text" name="serap" id="psse<?= $idOk; ?>" value="<?= rupiah($ls_jns['nom_serap']); ?>">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-xs btn-success" type="submit" name="sbmpt"><i class="fa fa-check"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td><?= $ls_jns['stas']; ?></td>
                                        </tr>
                                        
                                    <?php } ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->