<?php
if (isset($_SESSION['response'])) {
    $response = $_SESSION['response'];
}

if (isset($response)):
    if ($response['alertDismissible']): ?>
        <div class="alert alert-<?php echo $response['alert']['alertType']; ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    <?php else: ?>
        <div class="alert alert-<?php echo $response['alert']['alertType']; ?>" role="alert">
    <?php endif; ?>
    <?php echo $response['message']['message']; ?>

    </div>

    <?php
    unset($_SESSION['response'], $response);
endif;
?>