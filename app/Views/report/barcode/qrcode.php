<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SAS Report</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="<?= base_url('custom/image/logo.png') ?>" type="image/x-icon" />
</head>
<style>
    table {
        width: auto;
    }

    /* td {
        width: 330px;
        border: 1px solid black;
    } */

    td {
        width: 10px;
        border: 1px solid black;
    }

    /* img {
        float: left;
        vertical-align: top;
    } */

    /* p {
        line-height: 0.9;
    } */
    p {
        line-height: 0.5;
    }

    .company {
        font-size: 15px;
        float: right;
    }

    .branch {
        font-size: 17px;
    }

    .product {
        font-weight: bold;
        font-size: 15px;
    }

    /* .code-asset {
        margin-left: 10px;
        font-size: 15px;
        margin-top: -10px;
        margin-bottom: 2;
    } */
    .code-asset {
        margin-bottom: 2;
    }

    .tab {
        margin-left: 130px;
        font-size: 15px;
    }
</style>

<body>
    <div class="wrapper">

        <table cellspacing="10" cellpadding="3" style="text-align: center;">

            <?php foreach ($data as $key => $row) : ?>
                <tr>
                    <!-- <td>
                        <img src="<?= $row['qr'] ?>">
                        <p class="company">PT. Sahabat Abadi Sejahtera</p>
                        <p class="product"><?= $row['product'] ?></p>
                        <p class="branch"><?= $row['branch'] ?></p>
                        <p class="code-asset"><?= $row['assetcode'] ?><span class="tab"><?= $row['date'] ?></span></p>
                    </td> -->

                    <td>
                        <img src="<?= $row['qr'] ?>">
                        <p class="code-asset"><?= $row['assetcode'] ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>