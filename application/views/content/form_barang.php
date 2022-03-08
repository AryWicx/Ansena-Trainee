<section>
    <div class="container mt-3">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="d-flex justify-content-between mb-4">
                    <h4> <?= $this->uri->segment(3) ? 'Edit' : 'Tambah' ?> <?= isset($title) ? ucfirst($title) : "" ?></h4>
                </div>
                <?php if(validation_errors()) echo '<div class="alert alert-warning" role="alert" >'.validation_errors().'</div>'; ?>
                <?php if($this->session->flashdata('massage')) echo $this->session->flashdata('massage'); ?>

                <form action="<?= base_url('barang/save') ?>" class="mb-5" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= isset($data['id']) ? $data['id'] : "" ?>">
                    <div class="form-group mb-3">
                        <label for="nama" id="nama" class="form-label required">Nama</label>
                        <input type="text" name="nama"  class="form-control" placeholder="Nama barang"  value="<?= isset($data['nama']) ? $data['nama'] : "" ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control" placeholder="Deskripsi barang"><?= isset($data['deskripsi']) ? $data['deskripsi'] : "" ?></textarea>
                    </div>
                    <?php if(!isset($data['id']) || !$data['id']){ ?>
                        <div class="form-group mb-3">
                            <label for="stok" class="form-label required" required>Stok</label>
                            <input type="text" name="stok" id="stok" class="form-control number" placeholder="Jumlah stok" value="<?= isset($data['stok']) ? $data['stok'] : "" ?>">
                        </div>
                    <?php } ?>
                    <div class="form-group mb-3">
                        <label for="harga_jual" class="form-label required" required>Harga jual</label>
                        <input type="text" name="harga_jual" id="harga_jual" class="form-control number" placeholder="Harga jual"  value="<?= isset($data['harga_jual']) ? $data['harga_jual'] : "" ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="harga_beli" class="form-label required" required>Harga Beli</label>
                        <input type="text" name="harga_beli" id="harga_beli" class="form-control number" placeholder="Harga beli"   value="<?= isset($data['harga_beli']) ? $data['harga_beli'] : "" ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="gambar" id="gambar" class="form-label required">Gambar</label>
                        <input type="file" name="gambar"  class="form-control" accept="image/png, image/jpeg, image/jpg">
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-blocks btn-primary" type="submit"><b><?= $this->uri->segment(3) ? 'Update' : 'Simpan' ?> </b></button>
                    </div>
                </form>
               
            </div>
        </div>
    </div>
</section>

<script>
    setInputFilter(document.getElementsByClassName("number"), function(value) {
        return /^\d*\.?\d*$/.test(value); 
    });
    
    function setInputFilter(textbox, inputFilter) {
        if(!textbox) return;
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
            for (var i = 0 ; i < textbox.length; i++) {
                textbox[i].addEventListener(event, function() {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    this.value = "";
                }
                });
            }

        });
    }

</script>