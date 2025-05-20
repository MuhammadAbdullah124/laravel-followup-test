<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Product Form</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>.cursor-pointer{cursor:pointer;}</style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Product Form</h2>
    <form id="productForm" class="row g-3 mb-4">
        <input type="hidden" id="id" name="id" />
        <div class="col-md-4"><input type="text" class="form-control" id="name" name="name" placeholder="Product Name" required></div>
        <div class="col-md-3"><input type="number" class="form-control" id="quantity" name="quantity" placeholder="Quantity in stock" min="0" required></div>
        <div class="col-md-3"><input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Price per item" min="0" required></div>
        <div class="col-md-2 d-grid"><button type="submit" class="btn btn-primary">Save</button></div>
    </form>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product name</th>
                <th>Quantity in stock</th>
                <th>Price per item</th>
                <th>Datetime submitted</th>
                <th>Total value number</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody id="productTableBody"></tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Sum:</th>
                <th id="totalSum"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(function() {
    let products = @json($products);

    const $tbody = $('#productTableBody'),
          $totalSum = $('#totalSum'),
          $id = $('#id'), $name = $('#name'), $quantity = $('#quantity'), $price = $('#price'),
          $form = $('#productForm');

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function renderTable() {
        $tbody.empty();
        let totalSum = 0;
        products.forEach(({id, name, quantity, price, submitted_at}) => {
            const totalValue = quantity * price;
            totalSum += totalValue;
            $tbody.append(`
                <tr>
                    <td>${escapeHtml(name)}</td>
                    <td>${quantity}</td>
                    <td>${price.toFixed(2)}</td>
                    <td>${submitted_at}</td>
                    <td>${totalValue.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-secondary edit-btn" data-id="${id}">Edit</button></td>
                </tr>
            `);
        });
        $totalSum.text(totalSum.toFixed(2));
    }

    function resetForm() {
        $form[0].reset();
        $id.val('');
    }

    $form.on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('products.saveOrUpdate') }}",
            type: 'POST',
            data: {
                id: $id.val(),
                name: $name.val(),
                quantity: $quantity.val(),
                price: $price.val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                products = data;
                renderTable();
                resetForm();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Validation failed'));
            }
        });
    });

    $tbody.on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const product = products.find(p => p.id === id);
        if (product) {
            $id.val(product.id);
            $name.val(product.name);
            $quantity.val(product.quantity);
            $price.val(product.price);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    renderTable();
});
</script>
</body>
</html>
