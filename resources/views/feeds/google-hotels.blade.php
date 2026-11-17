{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<listings xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <language>en</language>
    <date_time>{{ now()->toIso8601String() }}</date_time>
    @foreach($properties as $prop)
    {{-- Only the hotel's own data: a field the listing does not have is left out, never guessed. --}}
    <listing>
        <id>{{ $prop->id }}</id>
        <name>{{ $prop->name }}</name>
        <address format="simple">{{ $prop->address ?: ($prop->city . ', Bangladesh') }}</address>
        @if(!empty($prop->city))
        <city>{{ $prop->city }}</city>
        @endif
        @if(!empty($prop->postal_code))
        <postal_code>{{ $prop->postal_code }}</postal_code>
        @endif
        <country>BD</country>
        @if(!empty($prop->latitude) && !empty($prop->longitude))
        <latitude>{{ (float) $prop->latitude }}</latitude>
        <longitude>{{ (float) $prop->longitude }}</longitude>
        @endif
        @if(!empty($prop->contact_phone))
        <phone>{{ $prop->contact_phone }}</phone>
        @endif
        <category>hotel</category>
        @if(!empty($prop->star_rating))
        <star_rating>{{ (int) $prop->star_rating }}</star_rating>
        @endif
        <landing_page>{{ url('/hotels/' . $prop->id) }}</landing_page>
    </listing>
    @endforeach
</listings>
