<div class="stock">
    <div class="form-group row align-items-end">
        <div class="col-md-3">
            <label>Stok Kodu</label>
            <input type="text" class="form-control" name="stockItems[stockItem][{{ $i }}][sellerStockCode]">
        </div>
        <div class="col-md-3">
            <label>Stok</label>
            <input data-toggle="touchspin" type="text" data-step="1" data-decimals="0" 
                name="stockItems[stockItem][{{ $i }}][quantity]" data-max="999999"
                required>
        </div>
        <div class="col-md-4">
            <label>Fiyat</label>
            <input data-toggle="touchspin" type="text" data-bts-postfix="₺" data-step="0.01" 
                data-decimals="2" name="stockItems[stockItem][{{ $i }}][optionPrice]" 
                data-max="999999">
        </div>
        <div class="col-md-2">
            <a href="del" class="btn btn-warning btn-block">
                <i class="uil uil-minus-circle"></i>
            </a>
        </div>
    </div>
    <div class="attribute-stock mt-3">
        <div class="form-group row mb-0">
            @for ($j = 0; $j < 3; $j++)
            <div class="col-md-4 mb-3">
                <label>Özellik</label>
                <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][name]"
                    class="form-control mb-2" placeholder="İsim">
                <input type="text" name="stockItems[stockItem][{{ $i }}][attributes][attribute][{{ $j }}][value]"
                    class="form-control" placeholder="Değer">
            </div>
            @endfor
        </div>
    </div>
</div>