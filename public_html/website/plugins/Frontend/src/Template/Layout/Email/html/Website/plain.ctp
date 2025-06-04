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
                background-color: #FFFFFF;
                height: 100%;
            }
            
            body, td {
                color: #555555;
                font-family: Arial,Helvetica,sans-serif;
                font-size: 12px;
                line-height: 16px;
            }
            
            h1 {
                font-size: 20px;
                line-height: 30px;
                font-weight: 700;
                display: block;
                border-bottom: 1px solid #555555;
            }
            
            tr.odd {
                background-color: #F2F2F2;
            }
            
            tr.even {
                background-color: #FCFCFC;
            }

        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td>
                    <center>
                        <table align="center" cellpadding="0" cellspacing="0" width="600">
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td style="text-align: left;"><?= $this->fetch('content') ?></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </center>
                </td>
            </tr>
        </table>
    </body>
</html>