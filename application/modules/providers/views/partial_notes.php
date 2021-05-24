<?php foreach ($provider_notes as $provider_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($provider_note->provider_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($provider_note->provider_note_date, true); ?>
        </div>
    </div>
<?php endforeach; ?>
