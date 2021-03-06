<section>
    <div class="container mt-3">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Data <?= isset($title) ? ucfirst($title) : "" ?></h5>
                    <div class="col-sm-3">
                        <input type="date" id="tanggal" class="form-control form-control-sm">
                    </div>
                </div>
                <?php if($this->session->flashdata('massage')) echo $this->session->flashdata('massage'); ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped" id="data">
                        <thead>
                            <tr>
                                <?php if($show) foreach($show as $val): ?>
                                    <th scope="col"><?= ucfirst(str_replace('_',' ',$val)); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="pagination-container">
            <nav aria-label="Page navigation example">
                <ul class="pagination" id='pagination'></ul>
            </nav>
        </div>
    </div>
</section>

<script type='text/javascript'>
var get_url = '<?=base_url(($this->uri->segment(1) ?: 'barang')."/load_data/")?>';
var tanggal = '', link = '';
var pageno = 0;
var myModal = $('#myModal');
var first = true;

$(document).ready(function(){
    $('#pagination').on('click','a',function(e){
        e.preventDefault(); 
        pageno = $(this).attr('data-ci-pagination-page');
        createPagination(pageno, tanggal);
    });

    $('#tanggal').on('change', function(e){
        e.preventDefault(); 
        tanggal = $(this).val();
        createPagination(pageno, tanggal);
    });

    createPagination(0);

    function createPagination(pagno){
        $.ajax({
            url: get_url+pagno+'?tanggal='+tanggal,
            type: 'get',
            dataType: 'json',
            success: function(response){
            $('#pagination').html(response.pagination);
            createTable(response.result,response.row);
            }
        });
    }

    function createTable(result,no){
        no = Number(no);
        $('#data tbody').empty();
        if(result.length == 0){
            $('#data tbody').append("<tr><td class='text-center' colspan='<?=count($show)?>'>No Record found</td></tr>");
        }
        for(index in result){
            no+=1;
            var content = "<tr><td>"+no+"</td>";
            Object.keys(result[index]).map((key) => {
                if(key != 'id'){
                    content += "<td>"+ result[index][key] +"</td>";
                }
            });

            content += "</tr>";

            $('#data tbody').append(content);
        }

        inits();
    }

    function inits() {
        $('.btn-delete').on('click',function (e) {
            var modalToggle = document.getElementById('modal')
            myModal.show(modalToggle);
            $('#myModal .action').show();
            link = $(this).data('link')
            $('#myModal .message').text('Apakah anda yakin ingin menghapus '+$(this).data('nama')+'?')

            $('#ok').click(function (e) {
                e.stopImmediatePropagation()
                $.ajax({
                    url: link,
                    type: 'get',
                    success: function(response){
                        $('#myModal').modal('hide');
                        createPagination(pageno);
                    }
                });
            })
        })

        $('.btn-detail').on('click',function (e) {
            var modalToggle = document.getElementById('modal')
            myModal.show(modalToggle);
            link = $(this).data('link')
            $('#myModal .title').text('Detail Transaksi')
            $('#myModal .action').hide();

            
            $.ajax({
                url: link,
                type: 'get',
                dataType: 'json',
                success: function(response){
                    var template = `<table class="table"><tbody>`;
                    for(index in response){
                        console.log(response[index].nama)
                        template += `<tr>
                        <th scope="row"> 
                            ${response[index].nama || '' } 
                            ${response[index].qty && response[index].harga ? '<p>'+response[index].qty+'X '+response[index].harga+'</p>' : '' }
                        </th>
                        <td>${response[index].subtotal || '' }</td>
                        </tr>`
                    }
                    template += `</tbody></table>`;
                    $('#myModal .message').html(template)
                }
                
            });

            
        })
    }

});
    </script>