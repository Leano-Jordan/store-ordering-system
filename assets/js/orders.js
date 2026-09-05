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
        console.error("Orders table body not found.");
        return;
    }

    const sortSelect = document.getElementById("orderSort");

    if (!sortSelect) {
        console.error("Order sort control not found.");
        return;
    }

    const rows = Array.from(table.querySelectorAll("tr"));

    const validRows = rows.filter(row => row.cells.length >= 5);

    const invalidRows = rows.filter(row => row.cells.length < 5);

    const activeRows = validRows.filter(
        row => row.cells[3].innerText.trim() !== "Cancelled"
    );

    const cancelledRows = validRows.filter(
        row => row.cells[3].innerText.trim() === "Cancelled"
    );

    const sort = sortSelect.value;

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

            const rankA = order[statusA] ?? Number.MAX_SAFE_INTEGER;
            const rankB = order[statusB] ?? Number.MAX_SAFE_INTEGER;

            return rankA - rankB;

        });

    } else if (sort === "highest") {

        activeRows.sort((a, b) => {

            const totalA = parseFloat(
                a.cells[2].innerText.replace(/[^\d.]/g, "")
            );

            const totalB = parseFloat(
                b.cells[2].innerText.replace(/[^\d.]/g, "")
            );

            return (Number.isFinite(totalB) ? totalB : 0)
                - (Number.isFinite(totalA) ? totalA : 0);

        });

    } else if (sort === "lowest") {

        activeRows.sort((a, b) => {

            const totalA = parseFloat(
                a.cells[2].innerText.replace(/[^\d.]/g, "")
            );

            const totalB = parseFloat(
                b.cells[2].innerText.replace(/[^\d.]/g, "")
            );

            return (Number.isFinite(totalA) ? totalA : 0)
                - (Number.isFinite(totalB) ? totalB : 0);

        });

    } else if (sort === "newest") {

        activeRows.sort((a, b) => {

            const dateA = new Date(a.cells[4].innerText.trim());
            const dateB = new Date(b.cells[4].innerText.trim());

            const timeA = dateA.getTime();
            const timeB = dateB.getTime();

            return (Number.isFinite(timeB) ? timeB : 0)
                - (Number.isFinite(timeA) ? timeA : 0);

        });

    } else if (sort === "oldest") {

        activeRows.sort((a, b) => {

            const dateA = new Date(a.cells[4].innerText.trim());
            const dateB = new Date(b.cells[4].innerText.trim());

            const timeA = dateA.getTime();
            const timeB = dateB.getTime();

            return (Number.isFinite(timeA) ? timeA : 0)
                - (Number.isFinite(timeB) ? timeB : 0);

        });

    }

    activeRows.forEach(row => table.appendChild(row));
    cancelledRows.forEach(row => table.appendChild(row));
    invalidRows.forEach(row => table.appendChild(row));

}

let userInterfacing = false;
let interactionTimeout = null;
let ordersRefreshInProgress = false;

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
                            userInterfacing = false;
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
                            userInterfacing = false;
                        }, 3000);
                });
        }

                setInterval(function() {

            if (userInterfacing || ordersRefreshInProgress) {
                return;
            }

            const params = new URLSearchParams(window.location.search);
            const currentPage = params.get('page') || '1';
            const currentOrder = search ? search.value : '';

            ordersRefreshInProgress = true;

            fetch(
                `ajax/orders_refresh.php?page=${encodeURIComponent(currentPage)}&order=${encodeURIComponent(currentOrder)}`
            )
                .then(response => {

                    if (!response.ok) {
                        throw new Error(
                            `Orders refresh failed with HTTP ${response.status}.`
                        );
                    }

                    return response.text();
                })
                .then(html => {

                    if (!html) {
                        return;
                    }

                    const tbody = document.querySelector(".orders-table tbody");

                    if (!tbody) {
                        console.error("Orders table body not found.");
                        return;
                    }

                    tbody.innerHTML = html;

                    if (sort && sort.value !== "") {
                        sortOrders();
                    }

                    if (search && search.value !== "") {
                        searchOrders();
                    }

                })
                .catch(error => {
                    console.error("Orders refresh failed:", error);
                })
                .finally(() => {
                    ordersRefreshInProgress = false;
                });

        }, 5000);
    });