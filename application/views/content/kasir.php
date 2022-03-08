<section>
    <div class="container mt-3">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Kasir</h5>
                </div>
            </div>

                <div class="row no-padding kasir-hero">
                    <div class="col-sm-8 no-padding d-flex card-product-container"></div>
                    <div class="col-sm-4 no-padding">
                        <div class="card">
                            <div class="card-body">
                                <h5>Cart</h5>
                                <hr>
                                <table class="table table-light data-cart">
                                    <tbody></tbody>
                                </table>

                                <div class="card-cart"></div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</section>
        
<script>
$(document).ready(function(){
    $.ajax({
        url: "<?= base_url('kasir/get_data') ?>",
        type: 'get',
        dataType: 'json',
        success: function(response){
            var content = '';
            for(index in response){
                content += `<div class="card card-product" style="width: 10rem;">
                                <img class="card-img-top" style="height: 8rem;object-fit: cover;" src="${ response[index]['gambar'] || '' }" alt="Barang Gambar">
                                <div class="card-body">
                                    <p class="card-title"><b>${ response[index]['nama'] || '' }</b></p>
                                    <p class="card-text">${ response[index]['harga_jual'] || '' }</p>
                                    <div class="text-center">
                                        <a href="#" class="btn btn-primary add-to-cart" data-id="${ response[index]['id'] || '' }" >Add to cart</a>
                                    </div>
                                </div>
                            </div>`
            }
            $('.card-product-container').append(content);
            productReady();
        }
    });

    function productReady() {
        $('.add-to-cart').click(function (e) {
            e.stopImmediatePropagation()
            let id = $(this).data('id');
            let mines = $(this).data('mines');

            $.ajax({
                url: "<?= base_url('kasir/cart_add') ?>",
                type: 'post',
                dataType: 'json',
                data: {id,mines},
                success: function(response){
                   getCart();
                }
            });
        })
    }

    getCart();


    function getCart() {
        var total = 0;
        $.ajax({
            url: "<?= base_url('kasir/get_cart') ?>",
            type: 'get',
            dataType: 'json',
            success: function(response){
                var cart_content = '';
                for(index in response){
                    if(response[index]['qty'] !== 0){
                        cart_content += `<tr>
                                        <th scope="row">${ response[index]['nama'] || '' }
                                            <div class="text-left mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary pills add-to-cart" data-id="${ response[index]['id'] || '' }" data-mines="true" ><i class="fas fa-minus"></i></button>
                                                ${ response[index]['qty'] || '0' }
                                                <button type="button" class="btn btn-sm btn-outline-secondary pills add-to-cart" data-id="${ response[index]['id'] || '' }" ><i class="fas fa-plus"></i></button>
                                            </div>
                                        </th>
                                        <td class="text-left" width="150">
                                            <p>Price: <b>${ response[index]['harga_jual'] || '' }</b></p>
                                            <p class="mt-3">Sub: <b>${ response[index]['subtotal'] || '0' }</b></p>
                                        </td>
                                    </tr>`
                                    total = total + (response[index]['subtotal']||0)
                    }
                }
                $('.data-cart tbody').empty();
                $('.data-cart tbody').append(cart_content);
                $('.card-cart').empty();
                $('.card-cart').append(`<div class="d-grid gap-2"><a href="<?= base_url('kasir/checkout') ?>" class="btn btn-primary btn-block" >Checkout (${ total || '0' }) </button></div>`);
                if(total == 0){
                    $('.data-cart tbody').append('<td class="hide" colspan="2"><i>Cart is empty</i></td>');
                }else{
                    $('.data-cart tbody hide').remove();

                }
                
                productReady();
            }  
        })
    }  
})
</script>