<div class="row">
    <div class="col-12">
        <div class="form-heading">
            <h4>Add Concession</h4>
        </div>
    </div>
    <div class="col-12 col-md-5 col-xl-5">
        <div class="input-block local-forms">
            <label class="form-label text-uppercase font-weight-bold fs-6 ">{{ $concessions_item->name }}</label>
            <input type="hidden" name="{{ $concessions_item->name }}" id="{{ $concessions_item->name }}" value="{{ $concessions_item->name }}">
            <small class="text-danger font-weight-bold err_concession_item"></small>
        </div>
    </div>

    <div class="col-12 col-md-5 col-xl-5">
        <div class="input-block local-forms ">
            <label>Qty<span class="text-danger">*</span></label>
            <input type="number" name="qty" class="form-control qty number_only_val" value="{{ old('qty') }}"
                id="qty"  >
            <small class="text-danger font-weight-bold err_qty"></small>
        </div>
    </div>

    <div class="col-12 col-md-2 col-xl-2">
        <button class="btn btn-lg btn-secondary text-uppercase btn-bottom" type="button" id="add_button">+</button>
    </div>

    <div class="col-12 col-md-12 col-xl-12 mb-4">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary">Concession #</th>
                        <th class="text-uppercase text-secondary">Qty</th>
                        <th class="text-uppercase text-secondary">Unit Price</th>
                        <th class="text-uppercase text-secondary">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="input_field_table">
                    <tr id="no_product_row">
                        <td colspan="6" class="text-center text-danger text-uppercase">No Product Added!</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 col-md-9 col-xl-9 col-lg-9"></div>


    @if (Auth::user()->hasPermissionTo('Create_Order'))
        <div class="col-12">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Create</button>
            </div>
        </div>
    @endif
</div>

{{-- <div class="modal fade" id="updateItemQty" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="updateItemQtyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="updateItemQtyLabel">Update QTY</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateItemQty">
                    @csrf

                    <div class="mb-3">
                        <label for="edit_qty" class="form-label">QTY </label>
                        <input type="number" class="form-control" id="edit_qty" name="edit_qty">
                        <input type="hidden" class="form-control" id="edit_raw_id" name="edit_raw_id">
                        <input type="hidden" class="form-control" id="edit_retail_price" name="edit_retail_price">
                        <input type="hidden" class="form-control" id="edit_total_price" name="edit_total_price">
                        <small class="text-danger font-weight-bold err_edit_qty"></small>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="save_change()">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

<script>
    $(document).ready(function() {

        $(document).on("input", ".number_only_val", function() {
            var self = $(this);
            self.val(self.val().replace(/\D/g, ""));
        });

        $(document).on("input", ".decimal_val", function() {
            var self = $(this);

            self.val(self.val().replace(/[^0-9\.]/g, ''));

            if ((self.val().match(/\./g) || []).length > 1) {
                self.val(self.val().slice(0, -1));
            }
        });

        calculate_net_total()
    });


    document.getElementById('qty').addEventListener('input', function () {
        let val = this.value;
        if (val.length > 4) {
            this.value = val.slice(0, 4);
        }
    });


    $('#add_button').click(function(e) {
        e.preventDefault();

        var product = $('#product').val();
        var qty = $('#qty').val();
        var purchase_product = [];

        $('input[name="product_id[]"]').each(function() {
            purchase_product.push($(this).val());
        });

        var data = {
            'product': product,
            'qty': qty,
            'product_ids': product_ids
        }

        $('#loader').show()


    })

    function append_table(data) {
        $('#no_product_row').remove()
        // Increment count for unique row ID
        count++;

        // Build the HTML string
        var html = "<tr id='row_" + count + "'>";
        html += "<td>" + data.product_no + "</td>";
        html += "<td>";
        html += '<input type="hidden" name="product_ids[]" value="' + data.id + '" >';
        html += data.name + ' - ' + data.unit_name;
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="text" class="form-control request_qty number_only_val" style="width:75px" name="request_qtys[]" id="request_qtys_' +
            count + '" value="' + data.qty + '" >';
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="text" class="form-control retail_price decimal_val" style="width:120px" name="retail_prices[]" id="retail_prices_' +
            count + '" value="' + data.retail_price + '" >';
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="hidden" class="form-control total_price decimal_val" style="width:75px" name="total_prices[]" id="total_prices_' +
            count + '" value="' + data.total_price + '" >';
        html += "<span class='row_total_price' id='row_total_price_" + count + "'>" + parseFloat(data.total_price)
            .toFixed(2) + "</span>";
        html += "</td>";

        html += "<td>";

        html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_product(' + count +
            ', \'' + data.id + '\', \'' + data.total_price + '\')" id="btn_' + count +
            '"><i class="fa fa-minus"></i></button>';
        html += "</td>";
        html += "</tr>";

        $('#purchaseItem_info_table').css('display', 'block');

        $('#input_field_table').append(html);

        product_ids.push(data.id);

        sub_total = parseFloat(sub_total) + parseFloat(data.total_price);
        $('#sub_total_amount').val(parseFloat(sub_total).toFixed(2))
        $('.sub_total_amount').text(parseFloat(sub_total).toFixed(2))
        calculate_net_total()
    }

    function remove_product(count, product_id, total_price) {
        product_id = parseInt(product_id)
        $.confirm({
            theme: 'modern',
            columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
            icon: 'far fa-question-circle text-danger',
            title: 'Are you Sure!',
            content: 'Do you want to Delete the Selected Product?',
            type: 'red',
            autoClose: 'cancel|10000',
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-green',
                    action: function() {
                        $('#row_' + count).remove();

                        product_ids = product_ids.filter(pro_id => pro_id !== product_id);

                        if (product_ids.length == 0) {
                            $('#input_field_table').append(
                                '<tr id="no_product_row"><td colspan="6" class="text-center text-danger text-uppercase">No Product Added!</td></tr>'
                            );
                        }

                        sub_total = parseFloat(sub_total) - parseFloat(total_price);
                        $('#sub_total_amount').val(parseFloat(sub_total))
                        $('.sub_total_amount').text(parseFloat(sub_total).toFixed(2))
                        calculate_net_total()
                    }
                },

                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-red',
                    action: function() {

                    }
                },
            }
        });
    }


    function clearError() {
        $('.err_product').text('');
        $('.err_qty').text('');
    }

    function clearInput() {
        $('#product').val('').change();
        $('#qty').val('');

    }
    document.getElementById('qty').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    document.getElementById('qty').addEventListener('input', function () {
        let val = this.value;
        if (val.length > 4) {
            this.value = val.slice(0, 4);
        }
    });

    document.querySelector('#input_field_table').addEventListener('input', function (event) {
        if (event.target.classList.contains('request_qty')) {
            let val = event.target.value.replace(/\D/g, '');
            if (val.length > 4) {
                event.target.value = val.slice(0, 4);
            } else {
                event.target.value = val;
            }
        }
    });

</script>
