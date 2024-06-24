<?php $totalProdutos = 0; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Map_Compras_<?php echo $result->idCompras ?>_<?php echo $result->nomeCliente ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css" />
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>
    <style>
        .table {

            width: 72mm;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div class="invoice-content">
                    <div class="invoice-head">
                        <table class="table">
                            <tbody>
                            <?php if ($emitente !== null && is_object($emitente)) { ?>
    <tr>
        <td style="width: 25%"><img src="<?php echo $emitente->url_logo; ?>"></td>
        <td>
            <span style="font-size: 17px;"><?php echo $emitente->nome; ?></span><br>
            <span style="font-size: 12px;">
                <span class="icon">
                    <i class="fas fa-fingerprint" style="margin:5px 1px"></i>
                    <?php echo $emitente->cnpj; ?><br>
                    <i class="fas fa-map-marker-alt" style="margin:4px 3px"></i>
                    <?php echo $emitente->rua . ', nº:' . $emitente->numero . ', ' . $emitente->bairro . ' - ' . $emitente->cidade . ' - ' . $emitente->uf; ?><br>
                </span>
                <span class="icon">
                    <i class="fas fa-comments" style="margin:5px 1px"></i>
                    E-mail: <?php echo $emitente->email . ' - Fone: ' . $emitente->telefone; ?><br>
                    <i class="fas fa-user-check"></i>
                    Responsável: <?php echo $result->nome; ?>
                </span>
            </span>
        </td>
<?php } else { ?>
    <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>> <a href="<?php echo base_url(); ?>index.php/mapos/emitente">Configurar</a></td>
<?php } ?>

                            </tbody>
                        </table>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td colspan="4" style="width: 50%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5>Fornecedor/Cliente</h5>
                                                    <span>
                                                        <?php echo $result->nomeCliente ?></span><br />
                                                    <span>
                                                        <?php echo $result->rua ?>,
                                                        <?php echo $result->numero ?>,
                                                        <?php echo $result->bairro ?></span><br />
                                                    <span>
                                                        <?php echo $result->cidade ?> -
                                                        <?php echo $result->estado ?></span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 0; padding-top: 0">
                        <?php if ($produtos != null) { ?>
                            <table class="table table-bordered table-condensed" id="tblProdutos">
                                <thead>
                                    <tr>
                                        <th style="font-size: 15px">Produto</th>
                                        <th style="font-size: 15px">Quantidade</th>
                                        <th style="font-size: 15px">Preço unit.</th>
                                        <th style="font-size: 15px">Sub-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($produtos as $p) {
                                        $totalProdutos = $totalProdutos + $p->subTotal;
                                        echo '<tr>';
                                        echo '<td>' . $p->descricao . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';
                                        echo '<td>R$ ' . ($p->preco ?: $p->precoVenda) . '</td>';
                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <?php if ($result->valor_desconto != 0 && $result->desconto != 0) { ?>
                                        <tr>
                                        <td colspan="3" style="text-align: right"><strong>Total: R$</strong></td>
                                        <td>
                                            <strong>
                                                <?php echo number_format($totalProdutos, 2, ',', '.'); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><strong>Desconto: R$</strong></td>
                                        <td>
                                            <strong>
                                                <?php echo number_format($result->valor_desconto - $totalProdutos, 2, ',', '.'); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="4" style="text-align: right">
                                            <h4 style="text-align: right">Total: R$
                                                <?php echo number_format($result->desconto != 0 && $result->valor_desconto != 0 ? $result->valor_desconto : $totalProdutos, 2, ',', '.'); ?>
                                            </h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php
                        } ?>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/matrix.js"></script>
    <script>
        window.print();
    </script>
</body>

</html>