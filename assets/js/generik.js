async function muatDataGenerik(url, keys) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    if (loading) loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch(url);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const dataList = await res.json();

        dataList.forEach(function (item) {
            const tr = document.createElement("tr");
            
            let tdHtml = "";
            keys.forEach(function (key) {
                tdHtml += "<td>" + (item[key] !== undefined ? item[key] : "") + "</td>";
            });

            tdHtml += "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";

            tr.innerHTML = tdHtml;
            tbody.appendChild(tr);
        });

    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"" + (keys.length + 1) + "\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        if (loading) loading.style.display = "none";
    }
}