let count = 0;
let total = 0;
let cart = [];
cart = JSON.parse(localStorage.getItem("cart")) || [];

let selectedCategory = "All";

let savedCount = localStorage.getItem('count');
let savedItems = localStorage.getItem('items');
let savedTotal = localStorage.getItem('total');

if (savedCount) {
    count = Number(savedCount);

    const cartElement = document.getElementById('cart');

    if (cartElement) {
        cartElement.innerHTML = 'Items: ' + count;
    }
}

if (savedItems) {

    const cartItems = document.getElementById('cart-items');

    if (cartItems) {
        cartItems.innerHTML = savedItems;
    }
}

if (savedTotal) {
    total = Number(savedTotal);

    const totalElement = document.getElementById('total');

    if (totalElement) {
        totalElement.innerHTML = 'Total: R' + total.toFixed(2);
    }
}
/*************        ************ ADD TO CART FUNCTION **************          ********/
function addToCart(id, productName, price) {
    count++;

    let existing = cart.find(item => item.id === id);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: productName,
            price: price,
            quantity: 1
        });
    }

    document.getElementById('cart').innerHTML = 'Items: ' + count;

    updateCartDisplay();

    total = total + price;

    document.getElementById('total').innerHTML = 'Total R' + total.toFixed(2);

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

/*************        *********** CLEAR THE CART FUNCTION ************          ********/

function clearCart() {

    count = 0;
    total = 0;
    cart = []; // Reset the cart array
    syncCart(); // Sync the cart to localStorage

    document.getElementById('customer').value = '';

    document.getElementById('cart').innerHTML = 'Items: 0';

    document.getElementById('total').innerHTML = 'Total: R0.00';


    localStorage.removeItem("cart");
    localStorage.removeItem("count");
    localStorage.removeItem("items");
    localStorage.removeItem("total");
}

/*************        ******* SYNC CART FUNCTION *******          ********/

function syncCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartDisplay();
}

/*************        ******* UPDATE THE CART DISPLAY FUNCTION *******          ********/

function updateCartDisplay() {

    const cartItems = document.getElementById("cart-items");

    if (!cartItems) {
        return;
    }

    let text = "";

    if (cart.length === 0) {
        cartItems.innerHTML = "<p style='text-align:center; color:#888;'>Your cart is empty.</p>";

        return;
    }

    if (!Array.isArray(cart)) {
        cart = []; // Ensure cart is initialized as an array
        syncCart(); // Sync the cart to localStorage
        return;
    }

    for (let i = 0; i < cart.length; i++) {

        let subTotal = cart[i].price * cart[i].quantity;

        text +=
            "<div class='cart-item'>" +
            "<div class='cart-name'>" +
            "<strong>" + cart[i].name + "</strong>" + "</div>" +

            "<div class='cart-controls'>" +
            "<button onclick=\"decreaseQuantity(" + cart[i].id + ")\">-</button>" +
            "<span>" + cart[i].quantity + "</span>" +
            "<button onclick=\"increaseQuantity(" + cart[i].id + ")\">+</button>" +
            "</div>" +

            "<div class='cart-price'>" + "R" + subTotal.toFixed(2) + "</div>" +

            "</div>";
    }

    cartItems.innerHTML = text;

}

/*************        ********** INCREASE QUANTITY FUNCTION **********          ********/

function increaseQuantity(id) {

    for (let i = 0; i < cart.length; i++) {

        if (cart[i].id === id) {
            cart[i].quantity++;
            count++;
            total += cart[i].price;
            break;
        }
    }

    document.getElementById('total').innerHTML = 'Total: R' + total.toFixed(2);

    updateCartDisplay();

    document.getElementById('cart').innerHTML = 'Items: ' + count;

    localStorage.setItem('items', document.getElementById('cart-items').innerHTML);
    localStorage.setItem('total', total);
    localStorage.setItem('count', count);

    syncCart();
}

/*************        ********** DECREASE QUANTITY FUNCTION **********          ********/

function decreaseQuantity(id) {

    for (let i = 0; i < cart.length; i++) {

        if (cart[i].id === id) {

            if (cart[i].quantity > 1) {

                cart[i].quantity--;
                total -= cart[i].price;
            } else {
                total -= cart[i].price;
                cart.splice(i, 1); // Remove the item from the cart
            }

            if (count > 0) {
                count--;
            }

            break;
        }
    }

    updateCartDisplay();

    document.getElementById('cart').innerHTML = 'Items: ' + count;

    document.getElementById('total').innerHTML = 'Total: R' + total.toFixed(2);

    localStorage.setItem('items', document.getElementById('cart-items').innerHTML);
    localStorage.setItem('total', total);

    syncCart();
}

/*************        ******** SEARCH FOR PRODUCTS FUNCTION **********          ********/

function searchProducts() {
    applyFilters();
}

/*************        ******** CLEAR SEARCH INPUT FUNCTION **********          ********/
function clearSearch() {
    document.getElementById("search").value = "";

    searchProducts();

    document.getElementById("search").focus();

    document.getElementById("clear-search").blur();
}

/*************        ********** FILTER PRODUCTS FUNCTION ************          ********/

function filterProducts(category, button) {

    //HIGHLIGHT ACTIVE BUTTON
    let buttons = document.getElementsByClassName("category-btn");

    for (let i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove("active");
    }
    button.classList.add("active");

    selectedCategory = category;

    applyFilters();
}

/*************        *********** APPLY FILTERS FUNCTION *************          ********/

function applyFilters() {
    let search = document.getElementById("search").value.toLowerCase();

    let products = document.getElementsByClassName("product");

    for (let i = 0; i < products.length; i++) {
        let name = products[i].querySelector("h2").textContent.toLowerCase();

        let category = products[i].dataset.category;

        let matchesSearch = name.includes(search);

        let matchesCategory = selectedCategory === "All" || category === selectedCategory;

        if (matchesSearch && matchesCategory) {
            products[i].style.display = "";
        } else {
            products[i].style.display = "none";
        }
    }
}

/*************        ********** PLACE THE ORDER FUNCTION ************          ********/

function placeOrder() {

    if (window.orderSubmitting) {
        return
    }

    let customer = document.getElementById('customer').value;

    if (customer.trim() === '') {
        alert("Please enter your name.");
        return;
    }

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.textContent = "Placing Order...";

    window.orderSubmitting = true;


    let items = "";

    for (let i = 0; i < cart.length; i++) {

        items += cart[i].name + " x " + cart[i].quantity;

        if (i < cart.length - 1) {
            items += "\n";
        }
    }

    let formData = new FormData();

    formData.append('customer', customer);
    formData.append('items', items);
    formData.append('total', total);
    formData.append('cart', JSON.stringify(cart));

    fetch('place_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {

            alert(data.message);

            if (data.success) {
                clearCart();
            }

            window.orderSubmitting = false;

            btn.disabled = false;
            btn.textContent = "Place Order";

        })
        .catch(error => {
            alert("Order failed.");
            window.orderSubmitting = false;

            btn.disabled = false;
            btn.textContent = "Place Order";
        });
}

/*************        ********** SEARCH ORDER FUNCTION ************          ********/

function searchOrders() {

    let input = document.getElementById("orderSearch").value.toLowerCase();

    let rows = document.querySelectorAll(".orders-table tbody tr");

    rows.forEach(function(row) {

        if (row.innerText.toLowerCase().includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

}

/*************        ********** SORT ORDERS FUNCTION ************          ********/

function sortOrders() {

    const table = document.querySelector(".orders-table tbody");

    const rows = Array.from(table.querySelectorAll("tr"));

    const activeRows = rows.filter(row => row.cells[3].innerText.trim() !== "Cancelled");

    const cancelledRows = rows.filter(row => row.cells[3].innerText.trim() === "Cancelled");

    const sort = document.getElementById("orderSort").value;

    if (sort === "status") {

        const order = {
            "Pending": 1,
            "Preparing": 2,
            "Ready": 3,
            "Collected": 4,
            "Cancelled": 5
        };

        activeRows.sort((a, b) => {

            const statusA = a.cells[3].innerText.trim();
            const statusB = b.cells[3].innerText.trim();

            return order[statusA] - order[statusB];

        });

    } else if (sort === "highest") {

        activeRows.sort((a, b) => {

            const totalA = parseFloat(a.cells[2].innerText.replace(/[^\d.]/g, ""));
            const totalB = parseFloat(b.cells[2].innerText.replace(/[^\d.]/g, ""));

            return totalB - totalA;

        });

    } else if (sort === "lowest") {

        activeRows.sort((a, b) => {
            const totalA = parseFloat(a.cells[2].innerText.replace(/[^\d.]/g, ""));
            const totalB = parseFloat(b.cells[2].innerText.replace(/[^\d.]/g, ""));

            return totalA - totalB;
        });

    } else if (sort === "newest") {

        activeRows.sort((a, b) => {

            const dateA = new Date(a.cells[4].innerText.trim());
            const dateB = new Date(b.cells[4].innerText.trim());

            return dateB - dateA;

        });

    } else if (sort === "oldest") {

        activeRows.sort((a, b) => {

            const dateA = new Date(a.cells[4].innerText.trim());
            const dateB = new Date(b.cells[4].innerText.trim());

            return dateA - dateB;

        });

    }

    activeRows.forEach(row => table.appendChild(row));
    cancelledRows.forEach(row => table.appendChild(row));

}

/*************        ********** SALES CANVAS AND CHART ************          ********/

document.addEventListener("DOMContentLoaded", function() {

    cart = JSON.parse(localStorage.getItem("cart")) || [];
    updateCartDisplay();

    const salesCanvas = document.getElementById("salesChart");

    if (salesCanvas && typeof Chart !== "undefined") {
        new Chart(salesCanvas, {
            type: "line",
            data: {
                labels: window.chartLabels,
                datasets: [{
                    label: "Sales (R)",
                    data: window.chartData
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});