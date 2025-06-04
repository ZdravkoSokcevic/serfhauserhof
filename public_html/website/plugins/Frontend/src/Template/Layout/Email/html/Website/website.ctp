<?php use Cake\Core\Configure; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="margin: 0; height: 100%; padding: 0;" xmlns="https://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!--<![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= $subject; ?></title>       
        <style type="text/css">

            body {
                background-color: #f2f2f2;
                height: 100%;
            }
            
            body, td {
                color: #504f54;
                font-family: Arial,Helvetica,sans-serif;
                font-size: 12px;
                line-height: 19px;
            }
            
            h1, h2 {
                font-size: 25px;
                line-height: 30px;
                font-weight: 700;
                display: block;
                color: #b19756;
                text-transform: uppercase;
                font-family: Times, serif;
                text-align: center;
            }
            
            h2 {
                text-align: left;
            }
            
            tr.odd {
                background-color: #f2f2f2;
            }
            
            tr.even {
                background-color: #FFFFFF;
            }
            
            span.color {
                color: #b19756;
            }
            
            a {
                color: #b19756;
                text-decoration: none;
            }
            
            .center {
                text-align: center;
            }
            
            .content {
                text-align: left;
                background-color: #FFFFFF;
                border: 20px solid #FFFFFF;
            }
            
            .logo {
                text-align: center;
                background-color: #FFFFFF;
                border: 20px solid #FFFFFF;
            }
            
            .seperator {
                width: 100%;
                line-height: 0;
                font-size: 0;
                padding: 0;
                margin: 0;
                height: 4px;
                background-color: #b19756;
            }
            
            .footer {
                text-align: center;
            }
            
            @media (max-width: 600px) {
            
                body *.hidden-mobile,
                body *[class=hidden-mobile]{        
                    width: 0 !important;
                    overflow: hidden !important;
                    float: left !important;
                    display: none !important;
                    max-height: 0px !important;
                    font-size: 0 !important;
                    line-height: 0 !important;
                    mso-hide: all !important;
                }
                
            }

        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td>
                    <center>
                        <table align="center" cellpadding="0" cellspacing="0" width="560">
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td class="logo"><?= $this->Html->image('/frontend/img/logo-big.png', ['fullBase' => true]); ?></td>
                            </tr>
                            <tr>
                                <td class="seperator">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="content"><?= $this->fetch('content') ?></td>
                            </tr>
                            <tr>
                                <td class="seperator">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="footer">
                                    <strong><?php echo Configure::read('config.default.hotel'); ?> &bull; <?php echo Configure::read('config.default.family-' . $this->request->params['language']); ?></strong> &bull; <?php echo Configure::read('config.default.street-' . $this->request->params['language']); ?> &bull; <?php echo Configure::read('config.default.city-' . $this->request->params['language']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="footer">
                                    Tel: <a href="tel:<?php echo Configure::read('config.default.phone-plain'); ?>"><?php echo Configure::read('config.default.phone'); ?></a> &bull; <a href="mailto:<?php echo Configure::read('config.default.email'); ?>"><?php echo Configure::read('config.default.email'); ?></a> &bull; <a href="<?php echo Configure::read('config.default.domain'); ?>"><?php echo str_replace(['http://','https://'], ['',''], Configure::read('config.default.domain')); ?></a>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </center>
                </td>
            </tr>
        </table>
    </body>
</html>