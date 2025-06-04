<?php if($this->Paginator->current() > 1 || $this->Paginator->hasNext()){ ?>
<tfoot>
    <tr>
        <td colspan="<?= isset($colspan) ? $colspan : 1; ?>">
            <ul>
                <?= $this->Paginator->first('<i class="fa fa-fast-backward"></i>', ['escape' => false]); ?>
                <?= $this->Paginator->hasPrev() ? $this->Paginator->prev('<i class="fa fa-step-backward"></i>', ['escape' => false]) : ''; ?>
                <?= $this->Paginator->numbers(['first' => 1, 'last' => 1]); ?>
                <?= $this->Paginator->hasNext() ? $this->Paginator->next('<i class="fa fa-step-forward"></i>', ['escape' => false]) : '' ; ?>
                <?= $this->Paginator->last('<i class="fa fa-fast-forward"></i>', ['escape' => false]); ?>
            </ul>
            <div clear="both"></div>
        </td>
    </tr>
</tfoot>
<?php } ?>