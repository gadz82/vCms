<style>
    *{font-family: helvetica}
    .logo{width:75px;}
    .title{border-bottom:1px solid #e5e5e5;}
    img.icon{width:80%;float:left;margin-top:15px;}
    td{padding:5px 0;}
    h1{font-family:helvetica;margin-bottom:5px;margin-top:0px;font-size: 22px;}
    h2{font-family:helvetica;margin-bottom:5px;margin-top:10px;font-size:20px; width:auto;color: #515151;}
    table p{font-family:helvetica;font-size:11px;color:#666;line-height:16px;margin-top:5px;margin-bottom:10px;}
    b{color: #515151;}
    ul{margin:0px;width:100%;padding:0px;color:#666; margin:0px;}
    li{padding:0px;margin:0px;color:#666;margin-left:15px; font-size:12px;width:100%;float:left;}
    .footer-container{border-top:1px solid #e5e5e5;width:100%;}
</style>
<page backtop="2mm" backbottom="15mm" backleft="2mm" backright="2mm">
    <page_footer>
        <div class="footer-container">
            <table style="width: 100%">
                <tr>
                    <td style="width:50%;text-align:left;">
                        <img class="logo" src="{{ base_url }}assets/site/images/logo.png">
                    </td>
                    <td style="width:50%; text-align:right;">
                        <p style="text-align:right;">www.gustourconad.it</p>
                    </td>
                </tr>
            </table>
        </div>
    </page_footer>
    <div class="title">
        <h1>{{post.titolo}}</h1>
    </div>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%; vertical-align:top;">
                <img style="width:95%;margin-top:10px;" src="{{ base_url }}files/small/{{post.immagine.filename}}">
            </td>
            <td style="width: 50%;vertical-align:top;">
                <p><strong>Ricetta {{ post.titolo }}</strong>, scopri gli ingredienti e come prepararla. Gli ingredienti sotto riportati servono per preparare {{ post.meta_numero_persone }} porzioni. Per la preparazione sono necessari circa {{ post.meta_tempo_preparazione }} minuti.</p>
                <div class="title">
                    <h2 style="margin-top:10px;">Ingredienti</h2>
                </div>
                <p>
                    {{ post.meta_ingredienti }}
                </p>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width:100%">
                <p>{{ shortcodes.shortcodify(post.testo) }}</p>
            </td>
        </tr>
    </table>
</page>