<?php
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
?>
<div class='flex-shrink-0 p-3 bg-white' id="sidebar">
    <a href='/' class='d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom'>
    <img src="icons/logo.png" height="30">
      <span class='fs-5 fw-semibold'>&nbsp;HireMonkey</span>
    </a>
    <ul class='list-unstyled ps-0'>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#dash-collapse' aria-expanded='false'>
          Dashboard
        </button>
        <div class='collapse' id='dash-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php' class='link-dark rounded'>Overview</a></li>
            <li><a href='index.php?l=invoicing' class='link-dark rounded'>Invoicing</a></li>
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#jobs-collapse' aria-expanded='false'>
          Jobs
        </button>
        <div class='collapse' id='jobs-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=job_new' class='link-dark rounded'>New</a></li>
            <li><a href='index.php?l=job_browse' class='link-dark rounded'>Browse</a></li>
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#customers-collapse' aria-expanded='false'>
          Contacts
        </button>
        <div class='collapse' id='customers-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=customer_new' class='link-dark rounded'>New</a></li>
            <li><a href='index.php?l=customer_import' class='link-dark rounded'>Import</a></li>
            <li><a href='index.php?l=customer_browse' class='link-dark rounded'>Browse</a></li>
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#kit-collapse' aria-expanded='false'>
          Equipment
        </button>
        <div class='collapse' id='kit-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=kit_new' class='link-dark rounded'>New</a></li>
            <li><a href='index.php?l=kit_browse' class='link-dark rounded'>Browse</a></li>
            <li><a href='index.php?l=cats' class='link-dark rounded'>Categories</a></li>
            <li><a href='index.php?l=catalog' class='link-dark rounded'>Catalog</a></li>
            <li><a href='index.php?l=repairs' class='link-dark rounded'>Repairs</a></li>
          </ul>
        </div>
      </li>
      <li class='border-top my-3'></li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#setup-collapse' aria-expanded='false'>
          Setup
        </button>
        <div class='collapse' id='setup-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=settings' class='link-dark rounded'>Settings</a></li>
            <li><a href='index.php?l=backup' class='link-dark rounded'>Backup</a></li>
          </ul>
        </div>
      </li>
    </ul>
    <?php
    if( trial() ) {
      echo "<div class='alert alert-warning'><center>Trial mode!</center></div>";
    }
    if( strtotime( '-1 day' ) > strtotime( company( "lastbackup" ) ) ) {
      echo "<div class='alert alert-warning'><center>Time for a backup?</center></div>";
    }
    if( isset( $licenceError ) ) {
      echo "<div class='alert alert-danger'><center>Licence error! Everything is read only!</center></div>";
    }
    ?>
  </div>