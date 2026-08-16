/* =========================================================
SwiftOrder POS — script.js  v0.8.4
   ========================================================= */

let count = 0;
let total = 0;
let cart = [];
let paymentMethod = 'cash_pmt';
let currentOrderRequestId = null;

// ── Bootstrap from localStorage (JSON source of truth) ──────────────
cart = loadCartFromStorage();

// Restore count + total scalars so they are ready before DOMContentLoaded
const savedCount = localStorage.getItem('count');
const savedTotal = localStorage.getItem('total');

if (savedCount) count = Number(savedCount);
if (savedTotal) total = Number(savedTotal);

let selectedCategory = 'All';

/* ─────────────────────────────────────────────────────────────────────
                                LOAD THE CART FROM STORAGE
   ───────────────────────────────────────────────────────────────────── */

function loadCartFromStorage() {
    const raw = localStorage.getItem('cart');
    if (!raw) return [];

    try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        localStorage.removeItem('cart');
        return [];
    }
}

/* ─────────────────────────────────────────────────────────────────────
                                ORDER TYPE
   ───────────────────────────────────────────────────────────────────── */

function setPaymentMethod(method, button) {
    paymentMethod = method;
    const hidden = document.getElementById('payment-method');
    if (hidden) hidden.value = method;

    document.querySelectorAll('.payment-method-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    button.classList.add('active');
}

/* ─────────────────────────────────────────────────────────────────────
                        CART DISPLAY HELPERS
   ───────────────────────────────────────────────────────────────────── */

function refreshCartMeta() {
    const cartEl = document.getElementById('cart');
    const totalEl = document.getElementById('total');
    const badgeEl = document.getElementById('cart-badge');
    const vatEl = document.getElementById('vat');

    let vatEnabled = false;
    let vatRate = 0;

    if (vatEl) {
        vatEnabled = vatEl.dataset.vatEnabled === '1';
        vatRate = Number(vatEl.dataset.vatRate);

        if (!Number.isFinite(vatRate) || vatRate < 0) {
            vatRate = 0;
        }
    }

    const vat = vatEnabled && vatRate > 0 ? total - (total / (1 * vatRate / 100)) : 0;

    if (cartEl) cartEl.innerHTML = count;
    if (totalEl) totalEl.innerHTML = 'R' + total.toFixed(2);
    if (vatEl) vatEl.innerHTML = 'R' + vat.toFixed(2);
    if (badgeEl) badgeEl.innerHTML = count + (count === 1 ? ' item' : ' items');
}

/* ─────────────────────────────────────────────────────────────────────
                            ADD TO CART
   ───────────────────────────────────────────────────────────────────── */

function addToCart(id, productName, price, image) {
    count++;

    const existing = cart.find(function(item) { return item.id === id; });

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: productName,
            price: price,
            image: image,
            quantity: 1
        });
    }

    total += price;

    refreshCartMeta();
    updateCartDisplay();
    persistCart();
}

/* ─────────────────────────────────────────────────────────────────────
CLEAR CART
   ───────────────────────────────────────────────────────────────────── */

function clearCart() {
    count = 0;
    total = 0;
    cart = [];

    const customerEl = document.getElementById('customer');
    if (customerEl) customerEl.value = '';

    refreshCartMeta();
    updateCartDisplay();

    localStorage.removeItem('cart');
    localStorage.removeItem('count');
    localStorage.removeItem('total');
    // 'items' key removed from all writes; clean up legacy key if present
    localStorage.removeItem('items');
}

/* ─────────────────────────────────────────────────────────────────────
PERSIST CART (single source of truth — JSON only)
   ───────────────────────────────────────────────────────────────────── */

function persistCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    localStorage.setItem('count', count);
    localStorage.setItem('total', total);
}

/* ─────────────────────────────────────────────────────────────────────
UPDATE CART DISPLAY
   ───────────────────────────────────────────────────────────────────── */

function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');
    if (!cartItems) return;

    if (!Array.isArray(cart) || cart.length === 0) {
        cartItems.innerHTML = "<p style='text-align:center; color:#bbb; padding:20px 0; font-size:13px;'>Your cart is empty.</p>";
        return;
    }

    let html = '';

    for (let i = 0; i < cart.length; i++) {
        const subTotal = (cart[i].price * cart[i].quantity).toFixed(2);

        html += `<div class='cart-item'>
            <img class="cart-image" src="assets/images/products/${cart[i].image}" alt="${cart[i].name}" onerror="this.onerror=null;this.remove();">
            <div class='cart-name'>
                ${cart[i].name}
            </div>

            <div class='cart-controls'>
                <button onclick="decreaseQuantity(${cart[i].id})">-</button>
                    <span>
                        ${cart[i].quantity}
                    </span>

                <button onclick='increaseQuantity(${cart[i].id})'>+</button>
            </div>
            <div class='cart-price'>
                R${subTotal}
            </div>
            </div>`;
    }

    cartItems.innerHTML = html;
}

/* ─────────────────────────────────────────────────────────────────────
INCREASE QUANTITY
   ───────────────────────────────────────────────────────────────────── */

function increaseQuantity(id) {
    for (let i = 0; i < cart.length; i++) {
        if (cart[i].id === id) {
            cart[i].quantity++;
            count++;
            total += cart[i].price;
            break;
        }
    }

    refreshCartMeta();
    updateCartDisplay();
    persistCart();
}

/* ─────────────────────────────────────────────────────────────────────
DECREASE QUANTITY
   ───────────────────────────────────────────────────────────────────── */

function decreaseQuantity(id) {
    for (let i = 0; i < cart.length; i++) {
        if (cart[i].id === id) {
            if (cart[i].quantity > 1) {
                cart[i].quantity--;
            } else {
                cart.splice(i, 1);
                break;
            }
        }
    }

    // BUG FIX #6: recalculate total from cart to avoid float accumulation drift
    total = cart.reduce(function(sum, item) {
        return sum + item.price * item.quantity;
    }, 0);

    if (count > 0) count--;

    refreshCartMeta();
    updateCartDisplay();
    persistCart(); // BUG FIX: count was not saved in original decreaseQuantity
}

/* ─────────────────────────────────────────────────────────────────────
SEARCH
   ───────────────────────────────────────────────────────────────────── */

function searchProducts() {
    applyFilters();
}

function clearSearch() {
    const searchEl = document.getElementById('search');
    if (searchEl) {
        searchEl.value = '';
        searchEl.focus();
    }
    searchProducts();
}

/* ─────────────────────────────────────────────────────────────────────
FILTER BY CATEGORY
   ───────────────────────────────────────────────────────────────────── */

function filterProducts(category, button) {
    document.querySelectorAll('.category-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    button.classList.add('active');
    selectedCategory = category;
    applyFilters();
}

/* ─────────────────────────────────────────────────────────────────────
APPLY FILTERS (search + category combined)
   ───────────────────────────────────────────────────────────────────── */

function applyFilters() {
    const searchEl = document.getElementById('search');
    const search = searchEl ? searchEl.value.toLowerCase() : '';
    const products = document.getElementsByClassName('product');

    for (let i = 0; i < products.length; i++) {
        const nameEl = products[i].querySelector('.product-name') || products[i].querySelector('h2') || products[i].querySelector('h3');
        const name = nameEl ? nameEl.textContent.toLowerCase() : '';
        const category = products[i].dataset.category;

        const matchesSearch = name.includes(search);
        const matchesCategory = selectedCategory === 'All' || category === selectedCategory;

        products[i].style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    }
}

/* ─────────────────────────────────────────────────────────────────────
PLACE ORDER
   ───────────────────────────────────────────────────────────────────── */

function placeOrder() {
    if (!currentOrderRequestId) {
        currentOrderRequestId = crypto.randomUUID();
    }

    if (window.orderSubmitting) return;

    const customerEl = document.getElementById('customer');
    const customer = (customerEl ? customerEl.value : '').trim();

    if (customer === '') {
        alert('Please enter a customer name.');
        return;
    }

    if (cart.length === 0) {
        alert('Cart is empty.');
        return;
    }

    const btn = document.getElementById('placeOrderBtn');
    if (!btn) return;

    btn.disabled = true;
    btn.textContent = 'Placing Order…';
    window.orderSubmitting = true;

    const formData = new FormData();
    formData.append('customer', customer);
    formData.append('cart', JSON.stringify(cart));
    formData.append('total', total);
    formData.append('payment_method', paymentMethod);
    formData.append('request_id', currentOrderRequestId);

    const csrfEl = document.getElementById('csrf_token');
    if (!csrfEl) {
        alert('Security token is missing.');
        window.orderSubmitting = false;
        btn.disabled = false;
        btn.textContent = 'Place Order';
        return;
    }

    formData.append('csrf_token', csrfEl.value);


    fetch('place_order.php', { method: 'POST', body: formData })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            alert(data.message);

            if (data.success) {

                clearCart()
                currentOrderRequestId = null;
            }

            window.orderSubmitting = false;
            btn.disabled = false;
            btn.textContent = 'Place Order';
        })
        .catch(function(error) {
            console.error(error);
            alert('Order failed. Please try again.');
            window.orderSubmitting = false;
            btn.disabled = false;
            btn.textContent = 'Place Order';
        });
}

/* ─────────────────────────────────────────────────────────────────────
PURCHASE ORDER HELPERS
   ───────────────────────────────────────────────────────────────────── */

function addPurchaseOrderRow(item) {
    item = item || null;
    const tbody = document.querySelector('#purchase-order-items tbody');
    if (!tbody) return;

    let options = '<option value="">Select Product</option>';

    window.poProducts.forEach(function(product) {
        const selected = (item && Number(item.product_id) === Number(product.id)) ? 'selected' : '';
        options += '<option value="' + product.id + '" ' + selected + '>' + product.name + '</option>';
    });

    const quantity = item ? item.quantity : 1;
    const price = item ? item.cost_price : '';
    const rowTotal = item ? (item.quantity * item.cost_price).toFixed(2) : '0.00';

    const row = document.createElement('tr');
    row.innerHTML =
        '<td><select name="product_id[]" required style="width:120%">' + options + '</select></td>' +
        '<td><input type="number" name="quantity[]" value="' + quantity + '" min="1" required oninput="calculatePurchaseOrderRow(this)"></td>' +
        '<td><input type="number" name="price[]" value="' + price + '" placeholder="0.00" min="0" step="0.01" required onfocus="if(this.value==0)this.value=\'\';" oninput="calculatePurchaseOrderRow(this)"></td>' +
        '<td class="line-total">R' + rowTotal + '</td>' +
        '<td><button type="button" class="action-btn delete-btn" onclick="removePurchaseOrderRow(this)">Remove</button></td>';

    tbody.appendChild(row);
}

function removePurchaseOrderRow(button) {
    button.closest('tr').remove();
    calculatePurchaseOrderTotal();
}

function calculatePurchaseOrderRow(input) {
    const row = input.closest('tr');
    const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name="price[]"]').value) || 0;
    row.querySelector('.line-total').textContent = 'R' + (quantity * price).toFixed(2);
    calculatePurchaseOrderTotal();
}

function calculatePurchaseOrderTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.line-total').forEach(function(cell) {
        grandTotal += parseFloat(cell.textContent.replace('R', '')) || 0;
    });
    const totalCell = document.getElementById('purchase-order-total');
    if (totalCell) totalCell.textContent = 'R' + grandTotal.toFixed(2);
}

/* ─────────────────────────────────────────────────────────────────────
DOM CONTENT LOADED
   ───────────────────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function() {

    // Re-read cart from JSON (single source of truth)
    cart = loadCartFromStorage();
    count = Number(localStorage.getItem('count')) || 0;
    total = Number(localStorage.getItem('total')) || 0;

    refreshCartMeta();
    updateCartDisplay();

    // Purchase order rows (edit screen)
    if (window.poItems && window.poItems.length > 0) {
        window.poItems.forEach(function(item) { addPurchaseOrderRow(item); });
    }

    // Sales chart
    const salesCanvas = document.getElementById('salesChart');
    if (salesCanvas && typeof Chart !== 'undefined') {
        new Chart(salesCanvas, {
            type: 'line',
            data: {
                labels: window.chartLabels,
                datasets: [{ label: 'Sales (R)', data: window.chartData }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});

/* ─────────────────────────────────────────────────────────────────────
SwiftOrder Version 0.9.1
Developer - Isaac Junior Lehlogonolo Maluleka
   ───────────────────────────────────────────────────────────────────── */