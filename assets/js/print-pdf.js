(function () {
    if (document.getElementById('pdf-print-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'pdf-print-styles';
    style.textContent = `
        .pdf-print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm;
            }

            html,
            body {
                width: auto !important;
                height: auto !important;
                overflow: visible !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sidebar,
            .user-profile-box,
            .pdf-print-button,
            .btn:not(.badge),
            button,
            form,
            .modal,
            .btn-group {
                display: none !important;
            }

            .main-content {
                display: block !important;
                width: 100% !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                background-image: none !important;
            }

            .main-content,
            .main-content * {
                visibility: visible !important;
            }

            .card,
            .card-custom {
                box-shadow: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            table {
                width: 100% !important;
                font-size: 9pt;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }

            .pdf-hide-column {
                display: none !important;
            }
        }
    `;
    document.head.appendChild(style);

    document.querySelectorAll('table').forEach(function (table) {
        const header = table.querySelector('thead tr:last-child');
        if (!header) {
            return;
        }

        const actionIndex = Array.from(header.cells).findIndex(function (cell) {
            return cell.textContent.trim().toLowerCase() === 'aksi';
        });
        if (actionIndex < 0) {
            return;
        }

        Array.from(table.rows).forEach(function (row) {
            if (row.cells[actionIndex]) {
                row.cells[actionIndex].classList.add('pdf-hide-column');
            }
        });
    });

    const printButton = document.createElement('button');
    printButton.type = 'button';
    printButton.className = 'pdf-print-button btn btn-danger rounded-pill px-3';
    printButton.title = 'Cetak atau simpan halaman ini sebagai PDF';
    printButton.innerHTML = '<i class="fas fa-file-pdf me-2" aria-hidden="true"></i>Cetak PDF';
    printButton.addEventListener('click', async function () {
        if (!window.simBkDesktop || typeof window.simBkDesktop.printToPdf !== 'function') {
            window.print();
            return;
        }

        printButton.disabled = true;
        try {
            const result = await window.simBkDesktop.printToPdf();
            if (!result.canceled) {
                window.alert('PDF berhasil disimpan di:\n' + result.filePath);
            }
        } catch (error) {
            console.error('Gagal membuat PDF:', error);
            window.alert('PDF gagal dibuat. Silakan coba lagi.');
        } finally {
            printButton.disabled = false;
        }
    });
    document.body.appendChild(printButton);
})();
