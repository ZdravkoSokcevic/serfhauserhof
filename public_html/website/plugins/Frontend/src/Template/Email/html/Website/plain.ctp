<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td><h1><?= $headline; ?></h1></td>
    </tr>
    <tr>
        <td style="text-align:right;"><?= date("d.m.Y H:i:s"); ?></td>
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