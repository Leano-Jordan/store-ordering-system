/* =========================================================
SwiftOrder POS — script.js  v0.8.4
   ========================================================= */

let count = 0;
let total = 0;
let cart = [];
let paymentMethod = 'cash_pmt';
let currentOrderRequestId = null;

const swiftOrderUserId = Number(window.SwiftOrderUserId);

const swiftOrderStoragePrefix = Number.isSafeInteger(swiftOrderUserId) &&
    swiftOrderUserId > 0 ? 'swiftOrder_user_' + swiftOrderUserId : 'swiftorder_invalid_user';

const CART_STORAGE_KEY = swiftOrderStoragePrefix + '_cart';
const COUNT_STORAGE_KEY = swiftOrderStoragePrefix + '_count';
const TOTAL_STORAGE_KEY = swiftOrderStoragePrefix + '_total';

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function createOrderRequestId() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID();
    }

    if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
        const bytes = new Uint8Array(16);

        window.crypto.getRandomValues(bytes);

        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;

        const hex = Array.from(bytes, function(byte) {
            return byte.toString(16).padStart(2, '0');
        }).join('');

        return (
            hex.slice(0, 8) + '-' +
            hex.slice(8, 12) + '-' +
            hex.slice(12, 16) + '-' +
            hex.slice(16, 20) + '-' +
            hex.slice(20)
        );
    }
    throw new Error(
        'Secure order request ID generation is unavailable.'
    );
}

// ── Bootstrap from localStorage (JSON source of truth) ──────────────
cart = loadCartFromStorage();

// Restore count + total scalars so they are ready before DOMContentLoaded
const savedCount = localStorage.getItem(COUNT_STORAGE_KEY);
const savedTotal = localStorage.getItem(TOTAL_STORAGE_KEY);

if (savedCount) count = Number(savedCount);
if (savedTotal) total = Number(savedTotal);

let selectedCategory = 'All';

/* ─────────────────────────────────────────────────────────────────────
                                LOAD THE CART FROM STORAGE
   ───────────────────────────────────────────────────────────────────── */

function loadCartFromStorage() {
    const raw = localStorage.getItem(CART_STORAGE_KEY);
    if (!raw) return [];

    try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        localStorage.removeItem(CART_STORAGE_KEY);
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

    const vat = vatEnabled && vatRate > 0 ? total - (total / (1 + vatRate / 100)) :
        0;

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

    localStorage.removeItem(CART_STORAGE_KEY);
    localStorage.removeItem(COUNT_STORAGE_KEY);
    localStorage.removeItem(TOTAL_STORAGE_KEY);
    // 'items' key removed from all writes; clean up legacy key if present
    localStorage.removeItem('items');
}

/* ─────────────────────────────────────────────────────────────────────
PERSIST CART (single source of truth — JSON only)
   ───────────────────────────────────────────────────────────────────── */

function persistCart() {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    localStorage.setItem(COUNT_STORAGE_KEY, count);
    localStorage.setItem(TOTAL_STORAGE_KEY, total);
}

/* ─────────────────────────────────────────────────────────────────────
UPDATE CART DISPLAY
   ───────────────────────────────────────────────────────────────────── */

function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');

    if (!cartItems) {
        return;
    }

    if (!Array.isArray(cart) || cart.length === 0) {
        cartItems.innerHTML = "<p style='text-align:center; color:#bbb; padding:20px 0; font-size:13px;'>Your cart is empty.</p>";
        return;
    }

    let html = '';

    for (let i = 0; i < cart.length; i++) {
        const itemId = Number.parseInt(cart[i].id, 10);
        const itemQuantity = Number.parseInt(cart[i].quantity, 10);
        const itemPrice = Number(cart[i].price);

        if (!Number.isSafeInteger(itemId) ||
            itemId <= 0 ||
            !Number.isSafeInteger(itemQuantity) ||
            itemQuantity <= 0 ||
            !Number.isFinite(itemPrice) ||
            itemPrice < 0
        ) {
            continue;
        }

        const safeName = escapeHtml(cart[i].name);
        const safeImage = escapeHtml(cart[i].image);
        const subTotal = (itemPrice * itemQuantity).toFixed(2);

        html += `<div class='cart-item'>
            <img class="cart-image" src="assets/images/products/${safeImage}" alt="${safeName}" onerror="this.onerror=null;this.remove();">
            <div class='cart-name'>
                ${safeName}
            </div>

            <div class='cart-controls'>
                <button onclick="decreaseQuantity(${itemId})">-</button>
                    <span>
                        ${itemQuantity}
                    </span>

                <button onclick='increaseQuantity(${itemId})'>+</button>
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

        const itemId = Number.parseInt(cart[i].id, 10);

        if (itemId !== id) {
            continue;
        }

        cart[i].quantity = Number(cart[i].quantity) + 1;

        count++;
        total += Number(cart[i].price);

        break;

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
        const itemId = Number.parseInt(cart[i].id, 10);

        if (itemId !== id) {
            continue;
        }

        const itemQuantity = Number.parseInt(
            cart[i].quantity, 10
        );

        if (itemQuantity > 1) {
            cart[i].quantity = itemQuantity - 1;
        } else {
            cart.splice(i, 1);
        }

        break;
    }

    count = cart.reduce(function(sum, item) {
        return sum + Number(item.quantity);
    }, 0);

    // recalculate total from cart to avoid float accumulation drift
    total = cart.reduce(function(sum, item) {
        return sum + Number(item.price) * Number(item.quantity);
    }, 0);


    refreshCartMeta();
    updateCartDisplay();
    persistCart();
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
        currentOrderRequestId = createOrderRequestId();
    }

    if (window.orderSubmitting) return;

    const customerEl = document.getElementById('customer');
    const customer = (customerEl ? customerEl.value : '').trim();

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


    fetch('place_order.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Order request failed with HTTP ' + response.status);
            }

            return response.json();

        })
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
        options += '<option value="' + product.id + '" ' + selected + '>' + escapeHtml(product.name) + '</option>';
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

    count = cart.reduce(function(sum, item) {
        return sum + Number(item.quantity);
    }, 0);

    total = cart.reduce(function(sum, item) {
        return sum + Number(item.price) * Number(item.quantity);
    }, 0);

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