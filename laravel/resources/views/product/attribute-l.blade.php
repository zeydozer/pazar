@if ($type == 'trendyol')
<div class="form-group row mb-0">
    @foreach ($attributes as $i => $attribute)
    
    <?php

    $value_d = null;
    
    if (isset($incoming->attributes)) :
        foreach ($incoming->attributes as $control) :
            if ($control->attributeId == $attribute->attribute->id) :

                if (isset($control->attributeValueId))

                    $value_d = $control->attributeValueId;

                else if (isset($control->attributeValue))

                    $value_d = $control->attributeValue;

            endif;
        endforeach;    
    endif;
    
    ?>
    
    <div class="col-md-4 mb-3">
        <input type="hidden" class="form-control" name="attributes[{{ $i }}][attributeId]" 
            value="{{ $attribute->attribute->id }}">
        <label>{{ $attribute->attribute->name }}</label>
        @if ($attribute->allowCustom)
        <input type="text" class="form-control" name="attributes[{{ $i }}][customAttributeValue]" 
            <?php if (isset($incoming->approved) && $incoming->approved === true) { ?> disabled <?php } ?>
            <?php if ($attribute->required) { ?> required <?php } ?> value="{{ $value_d }}">
        @else
        <select class="form-control" data-plugin="customselect" name="attributes[{{ $i }}][attributeValueId]"
            <?php if ($attribute->required) { ?> required <?php } ?> data-placeholder="Arayın"
            <?php if (isset($incoming->approved) && $incoming->approved === true) { ?> disabled <?php } ?>
            data-select2-id="attribute-{{ $i }}">
            <option value="">- Yok</option>
            @foreach ($attribute->attributeValues as $value)

            <?php $selected = $value_d == $value->id ? 'selected' : null ?>

            <option value="{{ $value->id }}" {{ $selected }}>{{ $value->name }}</option>
            @endforeach
        </select>
        @endif
    </div>
    @endforeach
</div>
@elseif ($type == 'system')
<div class="form-group row mb-0">
    @foreach ($attributes as $i => $attribute)
    <div class="col-md-4 mb-3">
        <label>{{ $attribute->name }}</label>
        @if ($attribute->option)
        <select class="form-control" data-plugin="customselect" name="attribute[{{ $attribute->id }}]"
            <?php if ($attribute->require) { ?> required <?php } ?> data-placeholder="Arayın"
            data-select2-id="attribute-{{ $i }}">
            <option value="">- Yok</option>
            @foreach (explode(',', $attribute->option) as $option)
            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
            @endforeach
        </select>
        @else
        <input type="text" class="form-control" name="attribute[{{ $attribute->id }}]"
            <?php if ($attribute->require) { ?> required <?php } ?>>
        @endif
    </div>
    @endforeach
</div>
@elseif ($type == 'n11')
<div class="form-group row mb-0">
    @foreach ($attributes as $i => $attribute)        
    <div class="col-md-4 mb-3">
        <label>{{ $attribute->name }}</label>
        <select class="form-control" data-plugin="customselect" name="attributes[attribute][{{ $i }}][value]"
            data-placeholder="Arayın" <?php if ($attribute->multipleSelect) { ?> multiple <?php } ?>>
            <option value="">- Yok</option>
            @if (is_array($attribute->valueList->value))
            @foreach ($attribute->valueList->value as $value)

            <?php
    
            $selected = null;

            $value_d = [null, null];

            if (isset($incoming->attributes->attribute)) :
                if (is_array($incoming->attributes->attribute)) :            
                    foreach ($incoming->attributes->attribute as $temp) :                        
                        if ($temp->id == $attribute->id) :

                            $value_d[0] = $temp->value;

                            if ($temp->value == $value->name) :

                                $selected = 'selected';

                                $value_d[1] = $value->name;
                            
                            endif;                        
                        endif;
                    endforeach;            
                elseif ($incoming->attributes->attribute->id == $attribute->id) :

                    $value_d[0] = $incoming->attributes->attribute->value;

                    if ($incoming->attributes->attribute->value == $value->name) :

                        $selected = 'selected';

                        $value_d[1] = $value->name;

                    endif;
                endif;
            endif;
            
            ?>

            <option value="{{ $value->name }}" {{ $selected }}>{{ $value->name }}</option>
            @endforeach
            @else

            <?php

            $selected = null;

            $value_d = [null, null];
    
            if (isset($incoming->attributes->attribute)) :
                if (is_array($incoming->attributes->attribute)) :            
                    foreach ($incoming->attributes->attribute as $temp) :
                        if ($temp->id == $attribute->id) :

                            $value_d[0] = $temp->value;

                            if ($temp->value == $attribute->valueList->value->name) :
                                
                                $selected = 'selected';

                                $value_d[1] = $attribute->valueList->value->name;

                            endif;
                        endif;             
                    endforeach;            
                elseif ($incoming->attributes->attribute->id == $attribute->id) :

                    $value_d[0] = $incoming->attributes->attribute->value;

                    if ($incoming->attributes->attribute->value == $attribute->valueList->value->name) :

                        $selected = 'selected';

                        $value_d[1] = $attribute->valueList->value->name;

                    endif;
                endif;
            endif;
            
            ?>

            <option value="{{ $attribute->valueList->value->name }}" {{ $selected }}>
                {{ $attribute->valueList->value->name }}
            </option>
            @endif
        </select>
        <input type="text" class="form-control mt-2" name="attributes[attribute][{{ $i }}][custom]" 
            placeholder="Özel" value="{{ trim($value_d[0]) != trim($value_d[1]) ? trim($value_d[0]) : null }}">
        <input type="hidden" name="attributes[attribute][{{ $i }}][name]" value="{{ $attribute->name }}">
    </div>
    @endforeach
</div>
@elseif ($type == 'gittigidiyor')
<div class="form-group row mb-0">    
    @foreach ($attributes as $i => $attribute)
    <input type="hidden" name="specs[spec][{{ $i }}][name]" value="{{ $attribute->name }}">
    <div class="col-md-4 mb-3">
        <label>{{ $attribute->name }}</label>
        <select class="form-control" data-plugin="customselect" name="specs[spec][{{ $i }}][value]"
            <?php if ($attribute->required) { ?> required <?php } ?> data-placeholder="Arayın"
            data-select2-id="attribute-{{ $i }}">
            <option value="">- Yok</option>

            <?php 
            
            $values = $attribute->values->value;

            if (!is_array($values)) 
            
                $values = [$values];

            $control = false;

            ?>

            @foreach ($values as $value)
            
            <?php
            
            $selected = null;

            if (isset($incoming->product->specs->spec))
            {
                if (!is_array($incoming->product->specs->spec))

                    $incoming->product->specs->spec = [$incoming->product->specs->spec];

                foreach ($incoming->product->specs->spec as $spec)
                {
                    if ($spec->name == $attribute->name && $spec->value == $value)
                    {
                        $selected = 'selected';

                        $control = true;
                    }
                }
            }
            
            ?>
            
            <option value="{{ $value }}" {{ $selected }}>{{ $value }}</option>
            @endforeach
        </select>

        <?php
            
        $value = null;

        if (isset($incoming->product->specs->spec) && !$control)
        {
            if (!is_array($incoming->product->specs->spec))

                $incoming->product->specs->spec = [$incoming->product->specs->spec];

            foreach ($incoming->product->specs->spec as $spec)
            {
                if ($spec->name == $attribute->name)

                    $value = $spec->value;
            }
        }
        
        ?>

        <input type="text" class="form-control mt-2" name="specs[spec][{{ $i }}][custom]" 
            placeholder="Özel" <?php if ($attribute->type == 'Checkbox') { ?> disabled <?php } ?>
            <?php if ($value) { ?> value="{{ $value }}" <?php } ?>>
    </div>
    <input type="hidden" name="specs[spec][{{ $i }}][type]" value="{{ $attribute->type }}">
    <input type="hidden" name="specs[spec][{{ $i }}][required]" value="{{ $attribute->required ? 'true' : 'false' }}">
    @endforeach
</div>
@elseif ($type == 'ciceksepeti')
<div class="form-group row mb-0">
    @foreach ($attributes as $i => $attribute)        
    <div class="col-md-4 mb-3">
        <label>
            {{ $attribute->attributeName }}
            @if ($attribute->varianter)
            <small>Varyant</small>
            @endif
        </label>
        <select class="form-control" data-plugin="customselect" name="attributes[{{ $i }}][valueId]"
            data-placeholder="Arayın" <?php if ($attribute->required) { ?> required <?php } ?>>
            <option value="">- Yok</option>
            @foreach ($attribute->attributeValues as $value)

            <?php
    
            $selected = null;

            if (isset($incoming->attributes)) :
                foreach ($incoming->attributes as $temp) :                        
                    if ($temp->id == $value->id) :
                            
                            $selected = 'selected';
                    
                    endif;
                endforeach;
            endif;
            
            ?>

            <option value="{{ $value->id }}" {{ $selected }}>{{ $value->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="attributes[{{ $i }}][id]" value="{{ $attribute->attributeId }}">
        <input type="hidden" name="attributes[{{ $i }}][textLength]" value="0">
    </div>
    @endforeach
</div>
@endif