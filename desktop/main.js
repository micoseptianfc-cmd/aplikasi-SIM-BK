const { app, BrowserWindow, dialog, ipcMain } = require('electron');
const fs = require('fs/promises');
const path = require('path');

let mainWindow;

ipcMain.handle('print-to-pdf', async (event) => {
    const printWindow = BrowserWindow.fromWebContents(event.sender);
    if (!printWindow || printWindow !== mainWindow) {
        throw new Error('Permintaan cetak PDF tidak valid.');
    }

    const pageReady = await printWindow.webContents.executeJavaScript(`(async () => {
        await document.fonts.ready;
        await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
        const content = document.querySelector('.main-content');
        return Boolean(content && content.innerText.trim());
    })()`);
    if (!pageReady) {
        throw new Error('Konten halaman belum tersedia untuk dicetak. Muat ulang halaman dan coba lagi.');
    }

    const { canceled, filePath } = await dialog.showSaveDialog(printWindow, {
        defaultPath: path.join(app.getPath('documents'), 'SIM-BK.pdf'),
        filters: [{ name: 'PDF', extensions: ['pdf'] }]
    });

    if (canceled || !filePath) {
        return { canceled: true };
    }

    const pdf = await printWindow.webContents.printToPDF({
        printBackground: true,
        landscape: true,
        pageSize: 'A4',
        margins: { top: 0.47, bottom: 0.47, left: 0.47, right: 0.47 }
    });
    if (pdf.length < 5 || pdf.subarray(0, 5).toString() !== '%PDF-') {
        throw new Error('Electron tidak menghasilkan dokumen PDF yang valid.');
    }
    await fs.writeFile(filePath, pdf);

    return { canceled: false, filePath };
});

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 720,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            nodeIntegration: false,
            contextIsolation: true
        },
        autoHideMenuBar: true, // Menyembunyikan menu bar bawaan agar rapi
        icon: path.join(__dirname, 'icon.ico') // Opsional jika ada ikon
    });

    // Langsung memuat halaman web PHP SIM BK
    mainWindow.loadURL('http://localhost/Sim-bk/index.php');

    // Jika koneksi gagal (misal server belum siap), otomatis muat ulang halaman setiap 2 detik sampai terbuka
    mainWindow.webContents.on('did-fail-load', () => {
        setTimeout(() => {
            mainWindow.loadURL('http://localhost/Sim-bk/index.php');
        }, 2000);
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

app.whenReady().then(() => {
    createWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});