/*************        ********** SEARCH ORDER FUNCTION ************          ********/

function searchOrders() {

    const searchInput = document.getElementById("orderSearch");

    if (!searchInput) {
        console.error("Order search input not found.");
        return;
    }

    const input = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll(".orders-table tbody tr");

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

    if (!table) {
        console.error("Table element not found.");
        return;
    }

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

let userInterfacing = false;
let interactionTimeout = null;

document.addEventListener("DOMContentLoaded",
    function() {

        const search = document.getElementById('orderSearch');
        const sort = document.getElementById('orderSort');

        if (search) {
            search.addEventListener("input",
                function() {

                    userInterfacing = true;

                    clearTimeout(interactionTimeout);
                    interactionTimeout = setTimeout(
                        function() {
                            userInterfacing =
                                false;
                        }, 3000);
                });
        }

        if (sort) {

            sort.addEventListener("change",
                function() {
                    userInterfacing = true;

                    clearTimeout(interactionTimeout);
                    interactionTimeout = setTimeout(
                        function() {
                            userInterfacing =
                                false;
                        }, 3000);
                });
        }

        setInterval(function() {

            if (userInterfacing) {
                return;
            }

            const params = new URLSearchParams(window.location.search);
            const currentPage = params.get('page') || '1';
            const currentOrder = document.getElementById('orderSearch') ? document.getElementById('orderSearch').value : '';

            fetch(`ajax/orders_refresh.php?page=${encodeURIComponent(currentPage)}&order=${encodeURIComponent(currentOrder)}`)
    .then(response => {
        if (!response.ok) {
            throw new Error(
                `Orders refresh failed with HTTP ${response.status}.`
            );
        }

        return response.text();
    })
    .then(html => {

        if (!html) return;

        const tbody = document.querySelector(".orders-table tbody");

        if (tbody) {
            tbody.innerHTML = html;

            if (sort && sort.value !== "") {
                sortOrders();
            }
            if (search && search.value !== "") {
                searchOrders();
            }
        }
    })
    .catch(error => {
        console.error("Orders refresh failed:", error);
    });
        }, 5000);
    });