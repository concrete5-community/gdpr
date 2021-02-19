<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var array $rows */
/** @var int $maxResults */
?>

<div class="ccm-ui">
    <?php
    if (count($rows) === $maxResults) {
        echo '<small>'.t('Note: Only %s records are shown.', $maxResults).'</small>';
    }
    ?>

    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <?php
                /** @var \Doctrine\DBAL\Schema\Column[] $columns */
                foreach ($columns as $column) {
                    echo '<th>'.e($column->getName()).'</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($rows as $row) {
                echo '<tr>';

                foreach ($row as $value) {
                    echo '<td>';
                    echo is_null($value) ? t('NULL') : e($value);
                    echo '</td>';
                }

                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
