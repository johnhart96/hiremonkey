/**
 *
 * This source code is subject to copyright.
 * Viewing, distributing, editing or extracting this source code will result in licence violation and/or legal action
 *
 * 
 * @package    HireMonkey
 * @author     John Hart
 * @copyright  2021 John Hart
 * @license    https://www.hiremonkey.app/licence.php
 */
// Includes
const electron = require('electron');
const app = electron.app;
const Menu = electron.Menu;
const BrowserWindow = electron.BrowserWindow;
const {shell} = require('electron')

const httpPort = getRandomInt(30000);


// Define App Menu
app.on('ready', () => {
  const template = [
    {
      label: 'Monkey',
      submenu: [
        {
          label: "Dashboard",
          click: function() { navigate( "index.php" ) },
          accelerator: 'CmdOrCtrl+D'
        },{
          label: "Change",
          click: function() { navigate( "static/company_select.php" ) }
        },{
          label: 'Settings',
          click: function() { navigate( "index.php?l=settings" ) }
        },{
          label: 'Backup',
          click: function() { navigate( "index.php?l=backup" ) }
        },{
          label: 'Quit',
          accelerator: 'CmdOrCtrl+Q',
          click: function() { app.quit(); }
        }
      ]
    },{
      label: "File",
      submenu: [
        {
          label: "New",
          submenu: [
            {
              label: 'Contact',
              click: function() { navigate( 'index.php?l=customer_new' ) },
              accelerator: "CmdOrCtrl+Shift+c"
            },{
              label: 'Equipment',
              click: function() { navigate( 'index.php?l=kit_new' ) },
              accelerator: "CmdOrCtrl+Shift+e"
            },{
              label: 'Job',
              click: function() { navigate( 'index.php?l=job_new' ) },
              accelerator: "CmdOrCtrl+Shift+j"
            }
          ]
        },{
          label: "Print",
          click: function() { print(); },
          accelerator: 'CmdOrCtrl+P'
        }
      ]
    },{
      label: "Edit",
      submenu: [
        {
          label: 'Undo',
          accelerator: 'CmdOrCtrl+Z',
          selector: 'undo:'
        },{
          label: 'Redo',
          accelerator: 'Shift+CmdOrCtrl+Z',
          selector: 'redo:'
        },{
          type: 'separator'
        },{
          label: 'Cut',
          accelerator: 'CmdOrCtrl+X',
          selector: 'cut:'
        },{
          label: 'Copy',
          accelerator: 'CmdOrCtrl+C',
          selector: 'copy:'
        },{
          label: 'Paste',
          accelerator: 'CmdOrCtrl+V',
          selector: 'paste:'
        },{
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
        },{
          label: "Support",
          click: function() { shell.openExternal( 'https://www.jh96.co.uk/helpdesk' ) }
        },{
          label: "Recover Licence",
          click: function() { shell.openExternal( 'https://hiremonkey.app/recover-licence.php' ) }
        },{
          label: "Purchase Licence",
          click: function() { shell.openExternal( 'https://hiremonkey.app/pricing.php' ) }
        }
      ]
    }
];
if( process.env.DEBUG ) {
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
      },{
        label: 'Debug Info',
        click: function() { navigate( "index.php?l=debug" ) }
      }
    ]
  });
}
function navigate( url ) {
    mainWindow.loadURL('http://'+server.host+':'+server.port+'/'+url);
}
  var mainMenu = Menu.buildFromTemplate(template);
  Menu.setApplicationMenu(mainMenu);
});

// PHP Server
const PHPServer = require('php-server-manager');
var options;
var errorDisplay;
if( process.env.DEBUG ) {
  errorDisplay = 1;
  console.log("Debug enabled");
} else {
  errorDisplay = 0;
}
if( process.platform == "win32" ) {
  options = {
    port: httpPort,
    host: "127.0.0.1",
    directory: __dirname,
    php: 'php/php.exe',
    directives: {
      display_errors: errorDisplay,
      expose_php: 1
    }
  }
} else {
  options = {
    port: httpPort,
    host: "127.0.0.1",
    directory: __dirname,
    directives: {
      display_errors: errorDisplay,
      expose_php: 1
    }
  }
}
const server = new PHPServer(options);

// Main Window
let mainWindow
function createWindow() {
  server.run();
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
  mainWindow.loadURL('http://'+server.host+':'+server.port+'/static/company_select.php');
  shell.showItemInFolder('fullPath');
  mainWindow.on('closed', function () {
    server.close();
    app.quit();
    mainWindow = null;
  });
}

// Init main window on win32
app.on('ready', createWindow)

// Window closed
app.on('window-all-closed', function () {
  if( process.platform !== 'darwin' ) {
    server.close();
    app.quit();
  }
})

// Init main window on macOS
app.on('activate', function () {
  if (mainWindow === null) {
    createWindow();
  }
})

// Disable security warnings
process.env['ELECTRON_DISABLE_SECURITY_WARNINGS'] = 'true';

// Functions
function print() {
  let win = BrowserWindow.getFocusedWindow();
	// let win = BrowserWindow.getAllWindows()[0];

	win.webContents.print(options, (success, failureReason) => {
		if (!success) console.log(failureReason);

		console.log('Print Initiated');
	});
}
function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}