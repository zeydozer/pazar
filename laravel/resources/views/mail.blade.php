@if ($subj == 'reset')

Değerli {{ $name }}; <br> Aşağıdaki bağlantıya giderek parolanızı yenileyebilirsiniz. <br><br> 
<a href="{{ url('token') .'?token='. $token }}">{{ url('token') .'?token='. $token }}</a>

@elseif ($subj == 'refresh')

Değerli {{ $name }}; <br> Parolanız başarıyla değiştirildi. <br><br> 
Yeni parolanız: <b>{{ $pass }}</b>

@elseif ($subj == 'invoice')

Merhaba <br>
{{ ucfirst($type) }} #{{ $id }} sipariş nolu faturanız ektedir. <br>
Sevgiler
<br><br>
<a href="//noone.com.tr">Noone</a>

@endif