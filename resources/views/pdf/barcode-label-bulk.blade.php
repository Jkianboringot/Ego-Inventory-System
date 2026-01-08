<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Barcode Labels</title>
    <style>
        @page {
            size: 50.8mm 76.2mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: white;
        }

        .label {
            width: 50.8mm;
            height: 76.2mm;
            padding: 2.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            page-break-inside: avoid;
        }

        .label:last-child {
            page-break-after: auto;
        }

        .price {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 1mm;
            color: #000;
        }

        .product-info {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 1.5mm 0;
        }

        .product-name {
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 1mm;
            word-wrap: break-word;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            max-height: 3.45em;
        }

        .product-description {
            font-size: 6pt;
            line-height: 1.1;
            color: #333;
            margin-top: 0.5mm;
            word-wrap: break-word;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            max-height: 2.2em;
        }

        .barcode-section {
            text-align: center;
            margin-top: auto;
        }

        .barcode-image {
            width: 100%;
            max-width: 45mm;
            height: auto;
            margin: 0 auto;
            display: block;
        }

        .barcode-number {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 0.5mm;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    @foreach($products as $product)
    <div class="label">
        <!-- Price -->
        <div class="price">
            {{ number_format($product->sale_price, 2) }}/PC
        </div>

        <!-- Product Information -->
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

        <!-- Barcode -->
        <div class="barcode-section">
            <img src="data:image/png;base64,{{ $product->barcodeImage }}" 
                 alt="Barcode" 
                 class="barcode-image">
            
            <div class="barcode-number">
                {{ $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>