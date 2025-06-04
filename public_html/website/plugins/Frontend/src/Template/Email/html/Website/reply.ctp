<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="center"><h1><?= $headline; ?></h1></td>
    </tr>
    <?php if(isset($content) && !empty($content)){ ?>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?= $content; ?></td>
    </tr>
    <?php } ?>
    <?php if(isset($infos) && !empty($infos)){ ?>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?= $infos; ?></td>
    </tr>
    <?php } ?>
</table>