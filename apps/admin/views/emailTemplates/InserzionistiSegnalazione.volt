<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Segnalazione problematica</title>
</head>

<body style="margin: 0; padding: 0; border: 0; background-color: #ddd; vertical-align: baseline; letter-spacing: -0.025em; font: inherit; font-size: 100%; line-height: 1;">

<table align="center" width="100%"
       style="max-width:600px; margin:0px auto; border-spacing:0; border-collapse:collapse; background-color:#82CDFF; color:#333333; text-align:center; font-size:32px; font-family:Arial, Verdana, Sans-serif; line-height:36px;">
    <tr style="border-bottom:1px solid #AAAAAA;">
        <td style="padding:10px 0px; font-weight:bold; font-size:22px; line-height:24px;">cmsio - Segnalazione
            Inserzionista
        </td>
    </tr>
</table>

<table align="center" width="100%"
       style="max-width:600px; margin:0px auto; border-spacing:0; border-collapse:collapse; background-color:#FFFFFF; color:#333333; text-align:center; font-size:32px; font-family:Arial, Verdana, Sans-serif; line-height:36px;">
    <tr style="border-bottom:1px solid #AAAAAA;">
        <td>
            <table width="100%"
                   style="max-width:600px; margin:0px auto; border-spacing:0; border-collapse:collapse; background-color:#F0F0F0; color:#333333; text-align:left; font-size:21px; font-family:Arial, Verdana, Sans-serif; line-height:25px;">
                <tr>
                    <td>
                    <td style="padding:10px; text-align:left;">{{ messaggio|e|nl2br }}</td>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%"
       style="max-width:600px; margin:0px auto; border-spacing:0; border-collapse:collapse; background-color:#FFFFFF; color:#333333; font-family:Arial, Verdana, Sans-serif;">

    <tr>
        <td style="padding:20px; font-weight:bold; font-size:20px; text-align:center; line-height:24px;">Dettaglio
            ordine
        </td>
    </tr>

    <tr>
        <td style="padding:0px 0px 20px 0px;">
            <table width="90%"
                   style="max-width:600px; margin:0px auto; border-spacing:0; border-collapse:collapse; background-color:#FFFFFF; color:#333333; text-align:left; font-size:16px; font-family:Arial, Verdana, Sans-serif; line-height:22px; word-break:break-all;">
                <tr style="border:1px solid #AAAAAA;">
                    <td style="padding:10px 0px 10px 10px; width:150px; text-align:left;"><strong>Cod.
                            riferimento</strong></td>
                    <td style="padding:10px 10px 10px 0px; width:430px; text-align:right;">{{ ordine.codice_riferimento_partner|e }}</td>
                </tr>

                <tr style="border:1px solid #AAAAAA;">
                    <td style="padding:10px 0px 10px 10px; width:150px; text-align:left;"><strong>Ragione
                            sociale</strong></td>
                    <td style="padding:10px 10px 10px 0px; width:430px; text-align:right;">{{ ordine.insegna|e }}</td>
                </tr>

                <tr style="border:1px solid #AAAAAA;">
                    <td style="padding:10px 0px 10px 10px; width:150px; text-align:left;"><strong>Partita IVA</strong>
                    </td>
                    <td style="padding:10px 10px 10px 0px; width:430px; text-align:right;">{{ ordine.partita_iva }}</td>
                </tr>

            </table>
        </td>
    </tr>

</table>

</body>
</html>