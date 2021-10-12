<?php
if( isset( $_POST['submitNew'] ) ) {
    if( trial() ) {
        if( entry_count( "cats" ) <= 10 ) {
            $add = true;
        } else {
            $add = false;
        }
    } else {
        $add = true;
    }
    if( $add ) {
        $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
        $insert = $db->prepare( "INSERT INTO `categories` (`name`) VALUES(:cat)" );
        $insert->execute( [ ':cat' => $name ] );
        $saved = true;
    }
}
if( isset( $_POST['submitEdit'] ) ) {
    $id = filter_var( $_POST['submitEdit'] , FILTER_SANITIZE_NUMBER_INT ) ;
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $update = $db->prepare( "UPDATE `categories` SET `name` =:cat WHERE `id` =:id" );
    $update->execute( [ ':cat' => $name , ':id' => $id ] );
    $saved = true;
}
if( isset( $_POST['submitDelete'] ) ) {
    $id = filter_var( $_POST['submitDelete'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `categories` WHERE `id` =:id" );
    $delete->execute( [ ':id' => $id ] );
    $saved = true;
}
?>
<div class="row">
    <div class="col">
        <h1>Categories:</h1>
        <hr />
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=kit_browse">Equipment</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Saved!</div>";
        }
        if( trial() ) {
            if( entry_count( "cats" ) >= 10 ) {
                echo "<div class='alert alert-danger'><strong>Error:</strong> You can only have 10 categories in trial mode!</div>";
            } else {
                echo "<div class='alert alert-warning'><strong>Warning:</strong> You can only add " . 10 - entry_count( "cats" ) . " more categories while in trial mode!</div>";
            }
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="btn-group">
            <?php
            if( trial() ) {
                if( entry_count( "cats" ) < 10 ) {
                    modalButton_green( "new" , "New" );
                } else {
                    echo "<a href='#' class='btn btn-secondary' disabled>New</a>";
                }
            } else {
                modalButton_green( "new" , "New" );
            }
            $dialog = "
                <div class='input-group'>
                    <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                    <input autofocus type='text' name='name' class='form-control'>
                </div>
                <input type='hidden' name='submitNew'>
            ";
            modal( "new" , "New category" , $dialog , "Save Cancel" ); 
            ?>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <table class="table table-bordered table-stripe">
            <tr>
                <th>Name</th>
                <th colspan="2"></th>
            </tr>
            <?php
            $getCategories = $db->query( "SELECT * FROM `categories`" );
            while( $cat = $getCategories->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $cat['name'] . "</td>";
                // Edit
                echo "<td width='1'>";
                $modal = "edit_" . $cat['id'];
                $dialog = "
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                        <input autofocus type='text' name='name' class='form-control' value='" . $cat['name'] . "'>
                    </div>
                    <input type='hidden' name='submitEdit' value='" . $cat['id'] . "'>
                ";
                modal( $modal , "Edit category" , $dialog , "Save Cancel" );
                modalButton( $modal , "Edit" );
                // Delete
                echo "<td width='1'>";
                $modal = "delete_" . $cat['id'];
                $dialog = "
                    Are you sure you want to delete this category?
                    <input type='hidden' name='submitDelete' value='" . $cat['id'] . "'>
                ";
                modal( $modal , "Delete?" , $dialog , "Yes No" );
                modalButton_red( $modal , "Delete" );
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>