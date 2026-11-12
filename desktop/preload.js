const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('simBkDesktop', {
    printToPdf: () => ipcRenderer.invoke('print-to-pdf')
});
