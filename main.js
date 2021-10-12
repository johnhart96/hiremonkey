const electron = require('electron')
// Module to control application life.
const app = electron.app
// Module for mennu
const Menu = electron.Menu
// Module to create native browser window.
const BrowserWindow = electron.BrowserWindow
const fs = require('fs');
const os = require('os');
const ipc = electron.ipcMain;
const path = require('path');
const shell = electron.shell;
const url = require('url')

/////////////////////////////

///////////////////////////////
// Copy paste fixed by this 

app.on('ready', () => {
//  createWindow() // commented for avoiding double window issue
  if (process.platform) {
    var template = [
      {
        label: 'Monkey',
        submenu: [
          {
            label: "Dashboard",
            click: function() { navigate( "index.php" ) },
            accelerator: 'CmdOrCtrl+D'
          },
          {
            label: 'Settings',
            click: function() { navigate( "index.php?l=settings" ) }
          },
          {
            label: 'Quit',
            accelerator: 'CmdOrCtrl+Q',
            click: function() { app.quit(); }
          }
        ]
      },{
        label: 'Edit',
        submenu: [
          {
          label: 'Undo',
          accelerator: 'CmdOrCtrl+Z',
          selector: 'undo:'
          }, {
            label: 'Redo',
            accelerator: 'Shift+CmdOrCtrl+Z',
            selector: 'redo:'
          }, {
            type: 'separator'
          }, {
            label: 'Cut',
            accelerator: 'CmdOrCtrl+X',
            selector: 'cut:'
          }, {
            label: 'Copy',
            accelerator: 'CmdOrCtrl+C',
            selector: 'copy:'
          }, {
            label: 'Paste',
            accelerator: 'CmdOrCtrl+V',
            selector: 'paste:'
          }, {
            label: 'Select All',
            accelerator: 'CmdOrCtrl+A',
            selector: 'selectAll:'
          }
        ]
      },{
        label: "View",
        submenu: [
          {
            role: 'resetZoom'
          },{
            role: 'zoomIn'
          },{
            role: 'zoomOut'
          },{
            role: 'zoom'
          }
        ]
      },{
        label: "Help",
        submenu: [
          {
            label: "About",
            click: function() { navigate( "index.php?l=about" ) }
          }
        ]
      }
    ];
    // Debug
    if (process.env.DEBUG) {
      template.push({
        label: 'Debugging',
        submenu: [
          {
            label: 'Dev Tools',
            role: 'toggleDevTools'
          },

          { type: 'separator' },
          {
            role: 'reload',
            accelerator: 'Alt+R'
          }
        ]
      });
    }
    var osxMenu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(osxMenu);
  } else {

  }
})

// PHP SERVER CREATION /////
const PHPServer = require('php-server-manager');
var options;
if(process.platform == "win32" ) {
  console.log('windows');
  options = {
    port: 5555,
    directory: __dirname,
    php: 'php/php.exe',
    directives: {
      display_errors: 1,
      expose_php: 1
    }
  }
  
} else {
  console.log('macos');
  options = {
    port: 5555,
    directory: __dirname,
    directives: {
      display_errors: 1,
      expose_php: 1
    }
  }
}
const server = new PHPServer(options);
//////////////////////////

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let mainWindow

function createWindow () {

  server.run();
  // Create the browser window.
  mainWindow = new BrowserWindow({
    width: 1024,
    height: 768,
    webPreferences: {
      nodeIntegration: true,
      contextIsolation: false,
      zoomFactor: 0.8,
      enableRemoteModule: false,
      scrollBounce: false
    },
    frame: true,
    minWidth: 1024,
    minHeight: 768
  });

  // and load the index.html of the app.
  mainWindow.loadURL('http://'+server.host+':'+server.port+'/')

/*
mainWindow.loadURL(url.format({
  pathname: path.join(__dirname, 'index.php'),
  protocol: 'file:',
  slashes: true
}))
*/
 const {shell} = require('electron')
 shell.showItemInFolder('fullPath')

  // Open the DevTools.
  // mainWindow.webContents.openDevTools()

  // Emitted when the window is closed.
  mainWindow.on('closed', function () {
    // Dereference the window object, usually you would store windows
    // in an array if your app supports multi windows, this is the time
    // when you should delete the corresponding element.
    // PHP SERVER QUIT
    server.close();
    app.quit();
    mainWindow = null;
  })
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', createWindow) // <== this is extra so commented, enabling this can show 2 windows..

// Quit when all windows are closed.
app.on('window-all-closed', function () {
  // On OS X it is common for applications and their menu bar
  // to stay active until the user quits explicitly with Cmd + Q
  if (process.platform !== 'darwin') {
    // PHP SERVER QUIT
    server.close();
    app.quit();
  }
})

app.on('activate', function () {
  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (mainWindow === null) {
    createWindow()
  }
})

function navigate( url ) {
  mainWindow.loadURL('http://'+server.host+':'+server.port+'/'+url);
}

ipc.on('print-to-pdf', event => {
  const pdfPath = path.join(os.tmpdir(),"temppdf.pdf");
  const win = BrowserWindow.fromWebContents(event.sender);

  win.webContents.printToPDF({}, (error, data) => {
    if(error) return console.log(error.message);
    console.log("printing pdf " + pdfPath);
    fs.writeFile(pdfPath, data, err => {
      if(err) return console.log(err.message);
      shell.openExternal('file://' + pdfPath );
      event.sender.send('wrote-pdf' , pdfPath );
    });
  });
});
process.env['ELECTRON_DISABLE_SECURITY_WARNINGS'] = 'true';

