{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<listings xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <language>en</language>
    <date_time>{{ now()->toIso8601String() }}</date_time>
    @foreach($properties as $prop)
    <listing>
        <id>{{ $prop->id }}</id>
        <name>{{ $prop->name }}</name>
        <address format="simple">{{ $prop->address ?: ($prop->city . ', Bangladesh') }}</address>
        <city>{{ $prop->city ?: "Cox's Bazar" }}</city>
        <province>Chittagong</province>
        <postal_code>4700</postal_code>
        <country>BD</country>
        <latitude>{{ (float)($prop->latitude ?: 21.4272) }}</latitude>
        <longitude>{{ (float)($prop->longitude ?: 92.0058) }}</longitude>
        <phone>+8801606352642</phone>
        <category>hotel</category>
        <star_rating>{{ (int)($prop->star_rating ?: 4) }}</star_rating>
        <landing_page>{{ url('/hotels/' . $prop->id) }}</landing_page>
    </listing>
    @endforeach
</listings>
