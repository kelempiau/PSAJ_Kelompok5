function exportToCSV(filename = 'Export') {
    // Try to find the management table specifically if multiple exist
    let table = document.querySelector(".table-container table") || document.querySelector("table");
    if (!table) {
        console.error("No table found to export");
        return;
    }

    let csv = [];
    const rows = table.querySelectorAll("tr");

    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll("td, th");
        // Skip the last column (usually actions)
        for (let j = 0; j < cols.length - 1; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            row.push('"' + text + '"');
        }
        if (row.length > 0) csv.push(row.join(","));
    }

    if (csv.length === 0) return;

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename + "_" + new Date().toISOString().slice(0, 10) + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
