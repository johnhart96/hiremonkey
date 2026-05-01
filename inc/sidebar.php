<div class='flex-shrink-0 p-3 bg-white' id="sidebar">
    <a href='/' class='d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom'>
    <img src="icons/logo.png" height="30">
      <span class='fs-5 fw-semibold'>&nbsp;HireMonkey</span>
    </a>
    <ul class='list-unstyled ps-0'>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#jobs-collapse' aria-expanded='false'>
          Jobs
        </button>
        <div class='collapse' id='jobs-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=job_new' class='link-dark rounded'>New</a></li>
            <li><a href='index.php?l=job_browse' class='link-dark rounded'>Browse</a></li>
            <li><a href='index.php?l=job_dispatched' class='link-dark rounded'>Dispatched</a></li>
            <li><a href='index.php?l=job_overdue' class='link-dark rounded'>Overdue returns</a></li>
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#customers-collapse' aria-expanded='false'>
          People
        </button>
        <div class='collapse' id='customers-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=customer_new' class='link-dark rounded'>New</a></li>
            <li><a href='index.php?l=customer_import' class='link-dark rounded'>Import</a></li>
            <li><a href='index.php?l=customer_browse' class='link-dark rounded'>Contacts</a></li>
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
            
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#invoice-collapse' aria-expanded='false'>
          Invoicing
        </button>
        <div class='collapse' id='invoice-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
            <li><a href='index.php?l=invoicing' class='link-dark rounded'>Invoice pile</a></li>
          </ul>
        </div>
      </li>
      <li class='mb-1'>
        <button class='btn btn-toggle align-items-center rounded collapsed' data-bs-toggle='collapse' data-bs-target='#service-collapse' aria-expanded='false'>
          Service
        </button>
        <div class='collapse' id='service-collapse'>
          <ul class='btn-toggle-nav list-unstyled fw-normal pb-1 small'>
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
    if( strtotime( '-1 day' ) > strtotime( company( "lastbackup" ) ) ) {
      echo "<div class='alert alert-warning'><center>Time for a backup?</center></div>";
    }
    
    ?>
    <a href="https://www.buymeacoffee.com/johnhart96" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me a Coffee" style="height: 40px !important;width: 160px !important;" ></a>
  </div>