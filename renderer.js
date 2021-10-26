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
// This file is required by the index.html file and will
// be executed in the renderer process for that window.
// All of the Node.js APIs are available in this process.
const ipc = require('electron').ipcRenderer;

const printPDFButton = document.getElementById('print-pdf');

printPDFButton.addEventListener('click', event => {
    console.log("print clicked");
    ipc.send('print-to-pdf');
});

ipc.on('wrote-pdf', (event,path) => {
    const message = `Wrote pdf to : ${path}`;
    document.getElementById('pdf-path').innerHTML = message;
});