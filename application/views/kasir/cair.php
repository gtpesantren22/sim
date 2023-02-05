<?php

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
                            <table id="" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr style="color: white; background-color: #008CFF; font-weight: bold;">
                                        <th>#</th>
                                        <th>Kode RAB</th>
                                        <th>PJ</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                        <th>Disetujui</th>
                                        <th>Akan dicairkan</th>
                                        <th>Ket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    foreach ($rls as $ls_jns) {
                                        $ids = explode('-', $ls_jns->id_realis);
                                        $idOk = $ids[0];
                                    ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td><?= $ls_jns->kode; ?></td>
                                            <td><?= $ls_jns->pj; ?></td>
                                            <td><?= $ls_jns->ket; ?></td>
                                            <td><?= rupiah($ls_jns->nominal); ?></td>
                                            <td><?= rupiah($ls_jns->nom_cair); ?></td>
                                            <td>
                                                <form action="<?= base_url('kasir/editSerap'); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?= $ls_jns->id_realis; ?>">
                                                    <input type="hidden" name="nom_cair" value="<?= $ls_jns->nom_cair; ?>">
                                                    <input type="hidden" name="kode_pengajuan" value="<?= $ls_jns->kode_pengajuan; ?>">
                                                    <input type="hidden" name="table" value="<?= $tbl_slct ?>">
                                                    <div class="input-group input-group-sm ">
                                                        <input type="text" class="form-control form-control-sm uang" name="serap" value="<?= $ls_jns->nom_serap; ?>" <?= $pjn->cair == 1 ? 'disabled' : '' ?> aria-describedby="button-addon2">
                                                        <button class="btn btn-success" type="submit" <?= $pjn->cair == 1 ? 'disabled' : '' ?> id="button-addon2"><i class="bx bx-check"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td><span class="badge bg-primary"><?= $ls_jns->stas; ?></span></td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">Total</th>
                                        <th><?= rupiah($dt->tunai_asal) ?></th>
                                        <th><?= rupiah($dt->tunai) ?></th>
                                        <th><?= rupiah($dt->tunai_serap) ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- CAIR BARANG -->
                        <br>
                        <div class="table-responsive">
                            <table id="3" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr style="color: white; background-color: #FD3550; font-weight: bold;">
                                        <th>#</th>
                                        <th>Kode RAB</th>
                                        <th>PJ</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                        <th>Disetujui</th>
                                        <th>Akan dicairkan</th>
                                        <th>Ket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    foreach ($rls2 as $ls_jns) {
                                        $ids = explode('-', $ls_jns->id_realis);
                                        $idOk = $ids[0];
                                    ?>
                                        <tr>
                                            <td>

                                            </td>
                                            <td><?= $ls_jns->kode; ?></td>
                                            <td><?= $ls_jns->pj; ?></td>
                                            <td><?= $ls_jns->ket; ?></td>
                                            <td><?= rupiah($ls_jns->nominal); ?></td>
                                            <td><?= rupiah($ls_jns->nom_cair); ?></td>
                                            <td>
                                                <form action="<?= base_url('kasir/editSerap'); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?= $ls_jns->id_realis; ?>">
                                                    <input type="hidden" name="nom_cair" value="<?= $ls_jns->nom_cair; ?>">
                                                    <input type="hidden" name="kode_pengajuan" value="<?= $ls_jns->kode_pengajuan; ?>">
                                                    <input type="hidden" name="table" value="<?= $tbl_slct ?>">
                                                    <div class="input-group input-group-sm ">
                                                        <input type="text" class="form-control form-control-sm uang" <?= $pjn->cair == 1 ? 'disabled' : '' ?> name="serap" value="<?= $ls_jns->nom_serap; ?>" aria-describedby="button-addon2">
                                                        <button class="btn btn-success" type="submit" id="button-addon2" <?= $pjn->cair == 1 ? 'disabled' : '' ?>><i class="bx bx-check"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td><span class="badge bg-danger"><?= $ls_jns->stas; ?></span></td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">Total</th>
                                        <th><?= rupiah($dt->brg_asal) ?></th>
                                        <th><?= rupiah($dt->brg) ?></th>
                                        <th><?= rupiah($dt->brg_serap) ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php if ($pjn->cair == 0) : ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <?= form_open('kasir/cairkan'); ?>
                                    <input type="hidden" name="kode_pengajuan" value="<?= $pjn->kode_pengajuan ?>">
                                    <div class="form-group mb-2">
                                        <label for="inputEmail3"">Jumlah akan
                                        dicairkan</label>
                                        <input type=" text" name="total" class="form-control" id="" value="<?= rupiah($dt->brg_serap + $dt->tunai_serap) ?>" readonly>

                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="inputPassword3"">Tanggal
                                        Pencairan</label>
                                    <input type=" text" class="form-control" id="date" name="tgl_cair" required>

                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="inputPassword3"">Penerima</label>
                                <input type=" text" class="form-control" id="" name="penerima" required>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="inputPassword3">Pencair</label>
                                        <input type=" text" name="kasir" class="form-control" value="<?= $user->nama ?>" readonly>
                                    </div>

                                    <div class="form-group mb-2">

                                        <button type="submit" name="cairkan" class="btn btn-success pull-right"><i class="bx bx-save"></i> Simpan pencairan</button>

                                    </div>
                                    <?= form_close(); ?>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->