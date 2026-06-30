<!DOCTYPE html>
<html>

<head>
    <title>
        Store Ordering System
    </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h1>
        Store Ordering System
    </h1>

    <p id="cart">
        Cart: 0
    </p>

    <div id="cart-items">

    </div>

    <p id="total">
        Total: R0
    </p>

    <button onclick="clearCart()">Clear Cart</button>

    <br><br>

    <input type="text" id="customer" placeholder="Your Name">

    <button onclick="placeOrder()">Place Order</button>


</body>

<script>
    let count = 0;
    let total = 0;

    let savedCount = localStorage.getItem('count');
    let savedItems = localStorage.getItem('items');
    let savedTotal = localStorage.getItem('total');

    if (savedCount) {
        count = Number(savedCount);

        document.getElementById('cart').innerHTML = 'Cart: ' + count;
    }

    if (savedItems) {

        document.getElementById('cart-items').innerHTML = savedItems;
    }

    if (savedTotal) {
        total = Number(savedTotal);

        document.getElementById('total').innerHTML = 'Total: R' + total;
    }



    function addToCart(products, price) {
        count++;

        document.getElementById('cart').innerHTML = 'Cart: ' + count;

        document.getElementById('cart-items').innerHTML = document.getElementById('cart-items').innerHTML + (
                count > 1 ? " + " : ""
            ) +
            products;

        total = total + price;

        document.getElementById('total').innerHTML = 'Total: R' + total;

        localStorage.setItem(
            'count', count
        );

        localStorage.setItem(
            'items', document.getElementById('cart-items').innerHTML
        );

        localStorage.setItem(
            'total', total
        );
    }

    function clearCart() {

        count = 0;
        total = 0;

        document.getElementById('cart').innerHTML = 'Cart: 0';

        document.getElementById('cart-items').innerHTML = '';

        document.getElementById('total').innerHTML = 'Total: R0';

        localStorage.clear();
    }

    function placeOrder() {
        let customer = document.getElementById('customer').value;

        if (customer == '') {
            alert('Enter your name');

            return;
        }

        alert(
            'Order placed by ' +
            customer +
            '\n' +
            document.getElementById('cart-items').innerHTML + '\nTotal: R' +
            total
        );
    }
</script>

</html>