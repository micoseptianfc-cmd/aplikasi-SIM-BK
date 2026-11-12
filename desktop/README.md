# SIM-BK Desktop

This Electron wrapper opens the existing SIM-BK site at `http://localhost/Sim-bk/` in an application window.

## Requirements on the target computer

- XAMPP installed with Apache and MySQL running.
- SIM-BK installed at `C:\xampp\htdocs\Sim-bk`.
- The SIM-BK database configured and available to the local MySQL server.

The portable executable contains the desktop wrapper only. It does not contain PHP, XAMPP, the website files, or the database.

## Build the Windows executable

From this directory, run:

```powershell
npm install
npm run build:win
```

The output is `dist/SIM-BK.exe`. Copy it to the target computer after setting up the requirements above.

To run the wrapper in development mode, start XAMPP and run `npm start`.
