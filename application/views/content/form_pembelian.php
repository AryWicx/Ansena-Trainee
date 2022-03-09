<section>
    <div class="container mt-3">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="d-flex justify-content-between mb-4">
                    <h5> <?= $this->uri->segment(3) ? 'Edit' : 'Tambah' ?> <?= isset($title) ? ucfirst($title) : "" ?></h5>
                </div>
                <?php if(validation_errors()) echo '<div class="alert alert-warning" role="alert" >'.validation_errors().'</div>'; ?>
                <?php if($this->session->flashdata('massage')) echo $this->session->flashdata('massage'); ?>
                
                <div class="form-group mb-3">
                    <select name="barang" id="barang" class="select2 form-control" data-placeholder="Pilih Barang"></select>
                </div>
                <form action="<?= base_url('pembelian/save') ?>" class="mb-5" method="post">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="list"></tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-blocks btn-primary" id="buttons" type="submit"><b>Beli </b></button>
                    </div>
                </form>
               
            </div>
        </div>
    </div>
</section>

<script>
    $('#barang').select2({
        minimumInputLength: 3,
        ajax: {
            url: '<?= base_url('pembelian/getBarang') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    s: params.term
                }
                return query;
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            harga: item.harga,
                            id: item.id
                        }
                    })
                };                
            }
        }
    }).on('select2:select', function (e) { 
        var id = $('#barang').select2('data')[0].id;
        var harga = $('#barang').select2('data')[0].harga;
        var text = $('#barang').select2('data')[0].text;
        var selected = false;
        
        $('.list > tr').each(function(index, tr) { 
            if(tr.id == id) selected = true;
        });

        if(!selected){
            $('.list').append(`<tr id="${id || ''}" harga="${harga || ''}">
                <input type="hidden" name="id[]" value="${id || ''}">
                <input type="hidden" name="harga[]" value="${harga || ''}">
                <th scope="row">${text || ''}</th>
                <td width="150"><input type="text" value="1" class="form-control number" name="qty[]" placeholder="Qty" required></td>
                <td width="150">${harga || ''}</td>
                <td width="150">${harga || ''}</td>
            </tr>`)
        }else{
            $('.list > tr#'+id).remove();
        }

        init()
    });


    function init() {
        
        $('input[name="qty[]"]').keyup(function () {
            var harga = $(this).closest('tr').attr('harga');
            $(this).closest('tr').find('td:last').text($(this).closest('tr').attr('harga')*$(this).val())
            var totalan = 0;    
            $('.list > tr').each(function(index, tr) { 
                totalan += parseInt($(this).find('td:last').text());
            });
            $('#buttons').text("Beli ("+totalan+")")
        })
        
        var totalan = 0;
        $('.list > tr').each(function(index, tr) { 
            totalan += parseInt($(this).find('td:last').text());
        });
        $('#buttons').text("Beli ("+totalan+")")
        

        setInputFilter(document.getElementsByClassName("number"), function(value) {
            return /^\d*\.?\d*$/.test(value); 
        });

        $('#barang').val('');
        $('#barang').trigger('change'); // Notify any JS components that the value changed
    }

    
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