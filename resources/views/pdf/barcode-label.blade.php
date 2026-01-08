<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Barcode Label</title>

<style>
@page {
    size: 60mm 40mm;
    margin: 0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    width: 60mm;
    height: 40mm;
    padding: 2mm 3mm 1.5mm 0.2mm; /* reduce bottom padding slightly to fit all */
    font-family: Arial, Helvetica, sans-serif;
    background: #fff;
}

/* PRICE */
.price {
    text-align: center;
    font-size: 22pt;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.3mm;
}

/* PRODUCT INFO */
.product-info {
    text-align: center;
    max-width: 57mm;
    margin: 0 auto;
}

.product-name,
.product-description {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
}

.product-name {
    font-size: 7.5pt;
    font-weight: bold;
    line-height: 1.15;
    margin-bottom: 0.3mm;
    -webkit-line-clamp: 3;
}

.product-description {
    font-size: 6pt;
    line-height: 1.1;
    margin-bottom: 0.3mm;
    -webkit-line-clamp: 3;
}

/* BARCODE */
.barcode-section {
    text-align: center;
    margin-top: 0.3mm;
}

.barcode-image {
    width: 100%;
    max-width: 57mm;
    height: auto;
}

.barcode-number {
    font-size: 22pt;      /* same as price */
    font-weight: bold;     /* same as price */
    line-height: 1;        /* same as price */
    text-align: center;    /* same as price */
    margin-top: 0.2mm;
    font-family: Arial, Helvetica, sans-serif; /* same as price */
}
</style>
</head>
<body>

<div class="price">
    {{ number_format($product->sale_price, 2) }}/PC
</div>

<div class="product-info">
    <div class="product-name">
        {{ strtoupper($product->name) }}
    </div>

    @if($product->description)
    <div class="product-description">
        {{ $product->description }}
    </div>
    @endif
</div>

<div class="barcode-section">
    <img src="data:image/png;base64,{{ $barcodeImage }}" class="barcode-image">

    <div class="barcode-number">
        {{ $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT) }}
    </div>
</div>

</body>
</html>
