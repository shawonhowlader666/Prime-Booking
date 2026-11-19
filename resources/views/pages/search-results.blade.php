@php
    $dest = trim($destination ?? request('destination', ''));
    $destLabel = !empty($dest) ? $dest : "Bangladesh";
    
    // Result counts
    $merged = $searchResults['merged_results'] ?? [];
    $paginator = $searchResults['paginator'] ?? null;
    $totalCount = isset($paginator) ? $paginator->total() : count($merged);
    $currentPage = isset($paginator) ? $paginator->currentPage() : 1;
    $lastPage = isset($paginator) ? max(1, $paginator->lastPage()) : 1;
    
    // Extract dynamic highlights, prices, and Schema items
    $prices = [];
    $hotelNames = [];
    $schemaItems = [];
    
    foreach ($merged as $idx => $mItem) {
        $mIsObj = is_object($mItem);
        $mPrice = (float)($mIsObj ? ($mItem->price_per_night ?? $mItem->price ?? 0) : ($mItem['price_per_night'] ?? $mItem['price'] ?? 0));
        if ($mPrice > 0) $prices[] = $mPrice;
        
        $mName = $mIsObj ? ($mItem->name ?? '') : ($mItem['name'] ?? '');
        if (!empty($mName) && count($hotelNames) < 5) {
            $hotelNames[] = $mName;
        }
    }
    
    $minFoundPrice = !empty($prices) ? min($prices) : 2388;
    $maxFoundPrice = !empty($prices) ? max($prices) : 11017;
    $minPriceText = '৳' . number_format($minFoundPrice, 0);
    $hotelHighlights = !empty($hotelNames) ? implode(', ', $hotelNames) : 'Sea Pearl, Ocean Paradise, Seagull Hotel';

    $checkinCarbon = \Carbon\Carbon::parse($checkIn ?: now());
    $checkoutCarbon = \Carbon\Carbon::parse($checkOut ?: now()->addDays(7));
    $guestCount = intval($guests ?: 2);
    $roomsCount = intval(request('rooms', 1));
    $currencyCode = \App\Helpers\CurrencyHelper::current();
    
    $metaTitle = "{$destLabel}: {$totalCount} properties found - Prime Booking";
    $metaDesc = "Find and book top rated hotels, luxury beach resorts, and budget stays in {$destLabel} on Prime Booking. Best guaranteed rates in BDT.";
    $canonicalUrl = route('search.index', request()->all());

    $sortBy = $sortBy ?? request('sort_by', 'featured');
    $sortOptions = [
        'featured'   => 'Best match',
        'price_low'  => 'Lowest price first',
        'price_high' => 'Highest price first',
        'rating'     => 'Top reviewed',
        'newest'     => 'Newest listings',
    ];
    if (request()->filled('lat') && request()->filled('lng')) {
        $sortOptions['distance'] = 'Distance';
    }
@endphp

<html lang="en" kite-lang="en" class="chrome no-js" dir="ltr"><head><style id="react-aria-pressable-style">@layer {
  [data-react-aria-pressable] {
    touch-action: pan-x pan-y pinch-zoom;
  }
}</style>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
        <meta name="referrer" content="origin-when-cross-origin">
    
    <title>Prime Booking | Hotels in {{ $destination ?? "Cox's Bazar" }} | Best Price Guarantee!</title>
    <link rel="icon" href="/favicon.ico">

    



<style type="text/css" data-selenium="critical-css">
    @-webkit-keyframes unripple{to{transform:scale(5)}}@-webkit-keyframes wave-300{0%{transform:translateX(-300px)}50%{transform:translateX(0)}to{transform:translateX(300px)}}@-webkit-keyframes wave-265{0%{transform:translateX(-265px)}50%{transform:translateX(0)}to{transform:translateX(265px)}}@-webkit-keyframes wave-180{0%{transform:translateX(-180px)}50%{transform:translateX(0)}to{transform:translateX(180px)}}@-webkit-keyframes wave-125{0%{transform:translateX(-125px)}50%{transform:translateX(0)}to{transform:translateX(125px)}}@-webkit-keyframes supplier-1-slide-in{0%{opacity:0;transform:translateX(24px)}12%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes supplier-2-slide-in{0%,12%{opacity:0;transform:translateX(24px)}24%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes supplier-3-slide-in{0%,24%{opacity:0;transform:translateX(24px)}36%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes supplier-4-slide-in{0%,36%{opacity:0;transform:translateX(24px)}48%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes supplier-5-slide-in{0%,48%{opacity:0;transform:translateX(24px)}60%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes supplier-6-slide-in{0%,60%{opacity:0;transform:translateX(24px)}72%,99%{opacity:1;transform:translateX(0)}to{opacity:0}}@-webkit-keyframes prev{0%{transform:translate3d(0,0,0)}to{transform:translate3d(0,-3px,0)}}@-webkit-keyframes active{0%{transform:translate3d(0,3px,0)}to{transform:translate3d(0,0,0)}}@-webkit-keyframes pin-red-drop{0%{bottom:200%}66%{bottom:50%;transform:scale(.8) rotate(10deg)}to{bottom:57%;transform:scale(1) rotate(10deg)}}@-webkit-keyframes pin-blue-drop-1{0%{bottom:200%}66%{bottom:41%;transform:scaleY(.8)}to{bottom:48%;transform:scale(1)}}@-webkit-keyframes pin-blue-drop-2{0%{bottom:200%}66%{bottom:52%;transform:scaleY(.8)}to{bottom:59%;transform:scale(1)}}@-webkit-keyframes pin-blue-drop-3{0%{bottom:200%}66%{bottom:37%;transform:scaleY(.8)}to{bottom:44%;transform:scale(1)}}@-webkit-keyframes pin-blue-drop-4{0%{bottom:200%}66%{bottom:52%;transform:scaleY(.8)}to{bottom:59%;transform:scale(1)}}*{box-sizing:border-box}figure,header,nav{display:block}img{border:0;vertical-align:middle}hr{box-sizing:content-box;height:0;border-image:initial;border-left:0;border-right:0;border-top:1px solid #eee}button,input{font:inherit;margin:0}button{overflow:visible;text-transform:none;-webkit-appearance:button}html{font-size:62.5%;text-size-adjust:100%;-webkit-font-smoothing:antialiased;-webkit-tap-highlight-color:transparent}
    body,dd{line-height:1.42857}button,input{font-family:inherit}a{color:#0283df;cursor:pointer;text-decoration:none}body,figure{margin:0}h4{line-height:1.1}p{margin:0 0 9px}ol,ul{margin-top:0;list-style:none;outline:0;padding:0}ol{margin-bottom:9px;list-style-type:none}dd{margin-left:0}ol ol,ol ul{margin-bottom:0}ol>li{border-bottom:none}[hidden]{display:none}.btn,.form-control{background-image:none}.form-control{height:40px;font-size:14px;background-color:#fff;padding:6px 12px;line-height:1.42857}.btn{margin-bottom:0;text-align:center;vertical-align:middle;touch-action:manipulation;cursor:pointer;white-space:nowrap;user-select:none;display:inline-block;line-height:1.4;font-weight:700;border-radius:3px;outline:0;border:0}.btn-primary{color:#fff;background-color:#0283df}.media-object{display:block}.modal{display:none}.pull-right{float:right}.hide{display:none}.flag{background-image:url(https://cdn6.agoda.net/images/desktop/bg-sprite-flags.png);background-repeat-x:no-repeat;background-repeat-y:no-repeat;display:inline-block;line-height:0;font-size:0;speak:none}.flag-18{width:24px;height:18px;vertical-align:text-top}.flag-18-us{background-position-x:-240px;background-position-y:-460px}.ficon-12{font-size:12px}.ficon-14{font-size:14px}.ficon-20{font-size:20px}.ficon{font-family:icons;vertical-align:middle;line-height:1;font-style:normal;speak:none;text-transform:none;-webkit-font-smoothing:antialiased;margin-bottom:3px;font-weight:400;display:inline-block;text-decoration:inherit}.oneline-searchbox-container{background-color:#515b62}.hotel-item-box{display:block;width:100%;height:100%;position:relative}.hotel-info{width:100%}.room-rate{text-align:right}.hotel-price-container{width:100%}.ssr-search-result-item{position:relative;display:block;min-height:220px;color:#000;padding:10px 5px}.filter-items{position:relative;padding-bottom:20px;overflow:hidden}.filter-menu{color:#333;cursor:text;font-size:1.4rem;font-weight:700;padding:18px 10px 7px 0}.filter-item-react{font-size:1.2rem;cursor:pointer;color:#5a5b5b;padding-top:4px;padding-bottom:3px;margin-top:1px;overflow:hidden}.filter-item-info{position:relative;padding-left:26px;line-height:1.8;cursor:pointer}.showMapBtns{top:0;right:0;color:#0283df;font-weight:700;text-align:center;display:block;white-space:nowrap;font-size:1.4rem}.map-legend-position-container{display:none}.container-agoda{margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px;clear:both}.navbar-menu{list-style-type:none;float:right;height:60px}.navbar-menu-item{display:block}.ssr-breadcrum{margin-left:0;margin-right:0}@media screen and (max-width:1100px){.ssr-container{width:100%;min-width:0}}.loading-panel{display:block}.container-agoda{width:980px}@media screen and (min-width:1175px){.container-agoda{width:1140px}}.form-control{color:#666;text-align:left}.language-menu-item,.main-header{position:relative}.main-header{height:60px;color:#666}.navbar-menu{display:block;text-align:center;line-height:normal;position:relative;margin:0;padding:0}.menu-saparator-right{position:relative;margin-right:21px}.l-col-3{width:25%}.l-col-4{width:33.3333%}.l-col-8{width:66.6667%}.l-col-9{width:75%}.l-col-12{width:100%}.ReviewScore{position:relative;text-align:center;vertical-align:middle}.SearchBoxTextDescription{color:#333;font-size:14px;padding:5px 0}.SearchBoxTextDescription__desc,.SearchBoxTextDescription__title{text-overflow:ellipsis}.SearchBoxTextDescription__title{white-space:nowrap;font-weight:600;overflow:hidden}.SearchBoxTextDescription__desc{line-height:1.25;color:#999}.SearchBoxTextDescription__child{position:absolute}.IconBox{display:inline-block;vertical-align:top;position:relative;background-color:#fff;cursor:pointer;box-shadow:rgba(0,0,0,.07) 0 4px 10px 0;margin:0 5px 0 0}.IconBox__child{display:inline-block;width:calc(100% - 60px)}.IconBox__icon{display:inline-block;vertical-align:middle;margin-bottom:0;padding:13px}.IconBox__wrapper{position:relative}.IconBox--checkIn{margin-right:0}.Searchbox{width:100%}.Searchbox__searchButton{position:relative;width:130px;background-color:#488bf8;font-size:14px;color:#fff;text-transform:uppercase;box-shadow:rgba(0,0,0,.2) 0 4px 10px 3px;cursor:pointer;border:0;border-radius:3px;overflow:hidden;padding:10px 0}@media screen and (max-width:1100px){.Searchbox__searchButton{width:100px;padding:10px 0}}.Searchbox__searchButton__text{font-weight:400}.Searchbox__searchButton--active{z-index:0}.Searchbox__searchButton--active>*,.Searchbox__wrapper,.rc-slider{position:relative}.rc-slider{height:14px;touch-action:none;border-radius:6px;padding:5px 0}.rc-slider,.rc-slider *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}.rc-slider-rail,.rc-slider-track{position:absolute;border-radius:6px}.rc-slider-rail{width:100%;background-color:#e9e9e9}.rc-slider-track{left:0}.rc-slider-handle{position:absolute;margin-left:-7px;margin-top:-5px;cursor:-webkit-grab;background-color:#fff;touch-action:pan-x;border-radius:50%}.rc-slider-step{position:absolute;width:100%;height:4px;background:0 0}.rc-slider{margin-left:7px;width:92%}.rc-slider-handle{width:20px;height:20px;border:1px solid #53b7ff}.rc-slider-track{background-color:#0283df}.rc-slider-rail,.rc-slider-track{height:2.5px;top:8px}.rc-slider-dot{display:none}.range-slider-label{display:inline-block}.histogram-bar{display:inline-block;background-color:#e0ecff}.filter-box{position:relative}.filteritem-prefix{font-weight:700;margin-right:10px;color:#333}.SearchBoxContainer{position:relative;margin:0 auto}@media screen and (max-width:1100px){.SearchBoxContainer{width:940px}}.FlightsFrameContainer--hidden,.filter-item-info input{display:none}.SearchBoxContainer--compact{min-height:70px}.oneline-searchbox{display:block;position:relative;font-size:0}.availability-message__text p,.oneline-searchbox-container{margin-bottom:0}.property-info-container{min-height:70px}.hotel-review-container{top:0}.discount-ribbon{position:relative;color:#fff;float:right;text-align:center;line-height:1;right:-2px;font-weight:700;border-radius:2px;margin:0;padding:5px 16px}.ssr-search-result-item{padding-top:7px;padding-bottom:7px}.bg-red{background-color:#ee595d}.star-orange{color:#ffa726}.info-icon{cursor:pointer;margin-bottom:0}.staticMapPanel{position:relative;width:100%;height:100%;min-height:80px;border-radius:3px;padding:0}.captionOverImage{position:absolute;display:block;bottom:10%;left:0;right:0;font-size:1.6rem;font-weight:700;color:#3c3c3c;text-transform:uppercase}.staticMapPanel{transition:box-shadow,.2s,ease-in-out}.ssr-placeholder{position:fixed;left:0;right:0;bottom:0;z-index:200;background:#fff;transition:opacity .3s ease-in-out}.ssr-placeholder-transparent{background:rgba(0,0,0,0);}.top-filter-placeholder{margin-left:-1px;margin-right:-1px;height:133px;position:relative;font-size:0;border:1px solid #dcdbdc}.top-filter-placeholder__cell{box-sizing:border-box;width:25%;height:100%;display:inline-block;vertical-align:top;padding:10px 15px 8px;border-left:1px solid #dcdbdc}.map-placeholder__higher{width:100%;height:105px;background-color:#e9ebee}.map-placeholder{width:100%;height:105px;background-color:#e9ebee}.placeholder-title-header{width:103px;height:12px;background-color:#e9ebee;margin-bottom:12px}.placeholder-title-header--sidebar{margin-bottom:22px}.list-filter-placeholder__checkbox{width:16px;height:16px;background-color:#fff;display:inline-block;vertical-align:top;border:1px solid #e8e8e8;border-radius:2px}.list-filter-placeholder__title{width:64px;height:12px;background-color:#e9ebee;display:inline-block;vertical-align:top;margin-left:8px;margin-top:2px}.list-filter-placeholder__row{margin-bottom:7px}.list-filter-placeholder--sidebar{border-bottom:1px solid #e8e8e8;padding:20px 0 17px}.histogram-placeholder{height:50px;margin-top:40px;border-bottom:2px solid #4d8ef8}.histogram-placeholder__bar{width:4%;background-color:#e0ecff;display:inline-block;vertical-align:bottom}.text-filter-placeholder{margin-top:-3px;height:40px;width:240px;border:1px solid #e8e8e8}@media (max-width:1024px){.text-filter-placeholder{width:200px}}.property-list-placeholder{margin-top:24px}.property-list-placeholder__item{margin-bottom:15px;display:inline-block;width:100%;height:100%;clear:both;border:1px solid #ddd}.box{position:relative;background:#e9ebee;overflow:hidden}.box__full{width:100%;height:229px}.box__fixed{height:15px;margin:0 0 13px}.box__fixed--300{width:300px}.box__fixed--180{width:180px}.box__fixed--125{width:125px}.placeholder-text__title{font-weight:600;color:#737373}.placeholder__animation-container{display:block;margin-top:10px}.placeholder__animation-container--supplier{width:100%}.placeholder__animation__supplier{margin-right:24px;display:inline-block;opacity:0}.placeholder__animation__supplier_agoda{max-width:75px}.orange-yellow{color:#f99e00}.cor-container-display{color:#646464;font-weight:700}.dropdown-wrapper{cursor:pointer}.sort-bar{width:100%;height:50px;display:table;margin-bottom:10px;background-color:#fff;border:1px solid #ddd}.sort-bar__toggle-label{line-height:12px}.sort-bar__description-wrapper{opacity:0}.sort-bar__toggle-description{position:absolute;top:0;left:0;display:block;text-transform:none;font-size:10px;line-height:10px;opacity:0;color:#777;transition:opacity .15s linear}.sort-bar__toggle-description--selected{opacity:1;color:#fff;background-color:#5392f9;transition:opacity .15s linear .15s}.sort-bar__divider{display:block;margin-top:0;height:50px;border-right:1px solid #ddd}.availability-message{width:100%;margin-bottom:15px;display:block;background-color:#fff3e0;position:relative;padding:10px 10px 10px 34px}.availability-message__text{display:inline-block;word-wrap:normal;float:none;width:calc(100% - 150px);vertical-align:middle;padding:0}.availability-message__gauge{vertical-align:middle;display:inline-block;padding:0 25px}.half-gauge{position:relative;width:100px;height:50px;overflow:hidden}.half-gauge-container{position:relative;width:100px;margin:auto}.half-gauge__label{display:block;position:absolute;top:auto;bottom:0;width:100%;font-size:20px;line-height:15px;color:#f99e00;text-align:center;z-index:1}.half-gauge__range{width:100px;height:50px;display:block;z-index:1;background-color:#ccc;border-radius:100px 100px 0 0}.half-gauge__level{position:absolute;z-index:0;width:100px;height:50px;background-color:#f99e00;transform-origin:center top 0;will-change:transform;border-radius:0 0 100px 100px}.filter-menu{overflow:hidden}.list-filter__dots-wrapper{position:absolute;bottom:0;left:1px;right:1px;height:28px;text-align:center;background:#fff;transition:all .15s linear}.list-filter__dots{margin:auto}.list-filter__dot{vertical-align:middle;width:4px;height:4px;background-color:#999;display:inline-block;cursor:pointer;border-radius:50%;margin:0 1px}.filter-list ul,.list-filter__items{list-style-type:none;margin:0;padding:3px}.property-name-text-editor{border-radius:0}.filters-list{position:relative}.filters-wrapper--horizontal{height:133px;z-index:110;position:relative}.filters-container{margin-bottom:30px}.filters-container--horizontal{margin-top:-1px;position:absolute;right:-1px;left:-1px}.filter-box{margin-bottom:2px;border-bottom:none;border-top:1px solid #e8e8e8}.filter-box--horizontal{display:inline-block;vertical-align:top;margin-bottom:-1px;margin-right:-1px;max-height:133px;min-height:133px;width:calc(25% + 1px);background:#fff;border-bottom:1px solid #dcdbdc;border-image:initial;border-left:1px solid #dcdbdc;border-right:1px solid #dcdbdc;overflow:hidden;padding:0 15px 8px;border-top:none}.sticky-filters{width:278px;top:85px;padding:0 12px 0 0}.sticky-filters__btn{font-size:14px;width:100%;text-align:center;max-width:none;white-space:normal;padding:10px}@media (max-width:1099px){.sticky-filters{width:238px}}.sticky-box{position:fixed}.pill-container{position:relative;height:19px;max-width:100%;padding-right:0;font-weight:700;font-size:1.1rem;display:inline-block;zoom:1;background-color:#fff;overflow:hidden;white-space:nowrap}.pill-item{display:inline-block;zoom:1;vertical-align:top;border-right:none;padding:0 5px;border-left:1px solid #eee}.pill-item--teaser{font-size:12px;line-height:19px;padding:0 8px 0 5px}.pill-item__icon{font-size:1.2rem;vertical-align:baseline}.pill-item__text{display:inline-block;text-overflow:ellipsis;vertical-align:top;overflow:hidden}.starAreaLine{line-height:100%;margin-bottom:8px}.ItemScroller{position:relative;overflow:hidden}.ItemScroller__item{position:absolute;opacity:0;visibility:hidden;transition:visibility 200ms,opacity 200ms}.ItemScroller__item--active{opacity:1;visibility:visible;animation:active 200ms}.ItemScroller__item--prev{animation:prev 200ms}.supplier-info-container{clear:both;padding-right:13px}.ReviewSection{padding-top:8px;text-align:right}.ReviewSection-item{display:inline-block;vertical-align:top;text-align:right;max-width:100px}.ReviewSection-scoreText{font-weight:600;font-size:12px;color:#333;word-wrap:break-word}.ReviewSection-count{font-weight:600;font-size:12px;color:#777}.ReviewSection-score{margin-left:5px}.property-card-content{border:1px solid #ddd;transition:box-shadow .2s ease-in-out}.ssr-search-result{clear:both}.animateMapWrapper,.form-group dd{position:relative}.static-map-pin-blue{position:absolute}.static-map-pin-red{position:absolute;bottom:57%;left:47%;transform:rotate(10deg)}.static-map-paper{width:100%;height:100%;border-radius:3px}.flatFilter-container{margin-bottom:10px}.flatFilter__label{color:#666;font-size:1.3rem;font-weight:700;display:inline-block;margin:0 2px 0 0}.flatFilter__list{display:inline-block;list-style-type:none;margin:0}.flatFilter__item{cursor:pointer;display:inline-block;font-size:1.2rem;font-weight:600;color:#666;border-right:1px solid #666;list-style:none;padding:0 10px;text-decoration:none}.flatFilter__item__preText{margin-left:7px;font-weight:700}.row::after,.row::before{content:" ";display:table}.row::after{clear:both}.navbar-header::after,.navbar-header::before{content:" ";display:table}.navbar-header::after{clear:both}.clearfix::after,.clearfix::before{content:" ";display:table}.clearfix::after{clear:both}header.navbar-agoda{margin:0 auto}.room-rate .price-container .price,.ssr-search-result-item h3{font-weight:700}.filter-list ul{margin:0 0 22px;padding:0}.filter-items ul,.sort-bar>a i{padding-left:0}.filter-menu i{font-size:1.2rem}.property-name input{display:inline;width:100%}.navbar-menu-item a,.oneline-searchbox>dl{position:relative;display:inline-block}.navbar-menu-item>a{display:block}@media (min-width:1100px){body .container-agoda{width:1100px;padding:0}}@media (max-width:1024px){body .container-agoda{width:940px;padding:0}}li.navbar-menu-item{float:left;width:auto;list-style:none;border:0}.menu-saparator-right::after{height:40px;top:10px;content:" ";position:absolute;z-index:1;border-right:1px solid #eee;right:-12px}.l-row::after,.l-row::before{content:" ";display:table}.l-row::after{clear:both}.ReviewScore i{position:inherit;color:#488bf8;font-weight:500;line-height:44px;margin-left:0}.ReviewScore span{position:absolute;color:#fff;right:0;left:0;font-weight:600;line-height:44px}.IconBox--checkOut::before{position:absolute;top:0;width:5px;height:100%;background:#fff}.IconBox--checkOut::after{position:absolute;top:10px;bottom:10px;border-right:1px solid #ccc}.Searchbox__searchButton--active::before{content:"";position:absolute;top:50%;left:50%;width:5%;padding-top:5%;transform:translate(-50%,-50%) scale(0);opacity:0;background:#84b5fe;border-radius:50%;transition:transform .3s ease-out,opacity .3s ease-out,-webkit-transform .3s ease-out}.range-slider-label span,.sort-bar>a{font-size:1.2rem;color:#5a5b5b;font-weight:600}.filteritem-prefix i{font-weight:400;font-size:1.3rem}.oneline-searchbox>dl{vertical-align:top;font-weight:400;padding:0}.box::after{content:" ";display:block;position:absolute;top:0;bottom:0;width:100%;height:100%;transform:translateX(0);background:#dddfe2}.box__full::after{animation:wave-265 1.5s ease-in-out infinite}.box__fixed--300::after{animation:wave-300 1.5s ease-in-out infinite}.box__fixed--180::after{animation:wave-180 1.5s ease-in-out infinite}.box__fixed--125::after{animation:wave-125 1.5s ease-in-out infinite}.sort-bar>a{position:relative;display:table-cell;vertical-align:middle;font-weight:700;text-align:left;color:#777;text-transform:uppercase;padding:0 10px}.sort-bar_col-5>a{width:18%}.sort-bar__toggle-link i{opacity:0;transition:opacity .15s linear}.availability-message__text span{color:#f99e00;font-weight:700}.half-gauge__range::after{position:absolute;top:6px;left:6px;z-index:1;width:88px;height:62px;content:"";background-color:#fff3e0;border-radius:100px 100px 0 0}.list-filter__dots-wrapper::before{content:"";display:inline-block;vertical-align:middle;height:100%}.urgency-messaging-container::after{content:"";display:table;clear:both}.ReviewSection-score i{font-size:54px}.ReviewSection-score span{font-size:20px;margin-left:2px}.ficon-aps-lock::before{content:"\f126"}.ficon-arrow-big-down::before{content:"\f128"}.ficon-book-now-pay-later::before{content:"\f14f"}.ficon-breakfast::before{content:"\f158"}.ficon-check-in::before{content:"\f183"}.ficon-check-out::before{content:"\f184"}.ficon-free-cancellation::before{content:"\f232"}.ficon-high-to-low::before{content:"\f258"}.ficon-human-large::before{content:"\f26a"}.ficon-icon-arrow-down::before{content:"\f26e"}.ficon-info-with-circle::before{content:"\f279"}.ficon-low-to-high::before{content:"\f2a7"}.ficon-metro-subway-station::before{content:"\f2c1"}.ficon-pay-at-the-place::before{content:"\f2ff"}.ficon-pin-star::before{content:"\f321"}.ficon-review-icon::before{content:"\f356"}.ficon-search-box::before{content:"\f370"}.ficon-star-1::before{content:"\f3d9"}.ficon-star-2::before{content:"\f3db"}.ficon-star-3::before{content:"\f3dd"}.ficon-star-35::before{content:"\f3de"}.ficon-star-4::before{content:"\f3df"}.ficon-star-5::before{content:"\f3e1"}.ficon-thin-arrow-down::before{content:"\f3f5"}.ficon-thin-arrow-left::before{content:"\f3f6"}.ficon-train-station::before{content:"\f416"}.ficon-travelers::before{content:"\f41d"}.navbar-menu-item a::before{right:37%}.navbar-menu-item a::after{right:38%}.navbar-menu-item>a::after,.navbar-menu-item>a::before{content:" ";display:table}.navbar-menu-item>a::after{clear:both}.sort-bar>a i{font-size:1rem;width:10px;height:10px;margin-right:3px;color:#777}.hotel-item-box .hotel-price-promo-container{position:absolute;right:0;height:100%;min-height:200px;background:#f8f8f9}.hotel-item-box>.media-box{position:absolute;left:0}.room-rate .price-container{zoom:1;padding-left:5px}.ssr-search-result-item .hotel-info{line-height:1.8}.ssr-search-result-item .hotel-name{line-height:1}.filter-item-info .checkbox-icon{width:16px;height:16px;text-align:center;line-height:1;position:absolute;top:3px;left:0;background:#fff;border:1px solid #e8e8e8;border-radius:2px}.CAPTCHA-CSS .captcha-error,.CAPTCHA-CSS .captcha-feedback-warning,.CAPTCHA-CSS .captcha-loading{display:none}.navbar-menu-item.sign-in-up{min-width:130px}.main-header .logo{float:left}.main-header .logo-with-header-links{margin:0 21px 0 0;padding:11px 10px 0 0}.navbar-header .header-links-container{display:inline-block;position:relative;margin:0}.SearchBoxTextDescription--searchText .SearchBoxTextDescription__desc{color:#488bf8}.filter-item-react .filter-item-info{display:block}.filter-item-react .filteritem-prefix{min-width:44px;text-align:left}.Searchbox--horizontal .IconBox--autocomplete{width:361px}@media screen and (max-width:1100px){.Searchbox--horizontal .IconBox--autocomplete{width:270px}}.Searchbox--horizontal .IconBox--occupancy{width:240px}@media screen and (max-width:1100px){.Searchbox--horizontal .IconBox--occupancy{width:200px}}.SearchBoxContainer--compact:not(.SearchBoxContainer--sticky){width:100%}.SearchBoxContainer--compact .IconBox--autocomplete,.SearchBoxContainer--compact .IconBox--checkIn,.SearchBoxContainer--compact .IconBox--checkOut,.SearchBoxContainer--compact .IconBox--occupancy{height:48px;padding:0 5px}.SearchBoxContainer--compact .IconBox--checkIn,.SearchBoxContainer--compact .IconBox--checkOut{width:177px}.SearchBoxContainer--compact .IconBox--checkIn,.oneline-searchbox-compact.oneline-searchbox .dates.dates.checkin{border-right:1px solid #999}.SearchBoxContainer--compact .SearchBoxTextDescription__child{top:14px}.SearchBoxContainer--compact .SearchBoxTextDescription--checkIn,.SearchBoxContainer--compact .SearchBoxTextDescription--checkOut{width:115px}.SearchBoxContainer--compact .Searchbox{max-height:70px;top:0;left:0;background-color:#20274d;animation:none;padding:11px 0}.SearchBoxContainer--compact .Searchbox__searchButton,.oneline-searchbox-compact.oneline-searchbox .actions button{height:48px}.SearchBoxContainer--compact .Searchbox__wrapper{width:1100px;margin:0 auto}@media screen and (max-width:1100px){.SearchBoxContainer--compact .Searchbox__wrapper{width:940px}}.oneline-searchbox .oneline-search-button{background-color:#488bf8;font-weight:700;position:relative;box-shadow:rgba(0,0,0,.2) 0 4px 10px 3px;overflow:hidden;padding:0}.oneline-searchbox .dates,.oneline-searchbox .occupancy,.oneline-searchbox .search{display:table;height:48px;background-color:#fff;font-size:13px;cursor:pointer;border:0}.oneline-searchbox .search{width:360px}@media screen and (max-width:1100px){.oneline-searchbox .search{width:270px}}.oneline-searchbox .occupancy{width:240px}@media screen and (max-width:1100px){.oneline-searchbox .occupancy{width:200px}}.property-info-container .hotel-name{zoom:1;font-size:1.6rem;display:inline;color:#3c3c3c;padding:0;text-decoration:none}.property-info-container .areacity-name{font-size:1.2rem;color:#0283df;font-weight:700}.property-info-container .star-rating{margin-right:3px}.hotel-review-container .property-review-container{text-align:right;padding-right:1px;color:#0283df}.hotel-review-container .review-score-container{font-size:1.6rem;font-weight:700}.hotel-review-container .review-item{padding:0 2px}.hotel-review-container .review-score-label{padding-right:3px;word-wrap:break-word;line-height:.8;display:inline;position:initial;top:initial;left:initial;width:initial;text-align:initial;color:inherit;font-size:inherit;font-weight:inherit}.hotel-price-promo-container .hotel-price-container{position:absolute;top:auto;bottom:10px;right:-4px;text-align:right}.hotel-price-promo-container .room-rate{clear:both;padding:5px 13px 0 5px}.LOGIN-CSS .facebook-login .link-account #link-account-ask-email-content.popup-login .respond-warning,.LOGIN-CSS .facebook-login .link-account #link-account-ask-name-content.popup-login .respond-warning,.hotel-price-promo-container .price-container,.hotel-price-promo-container .rpn-container{display:block}.hotel-price-promo-container .per-night-begins-with{font-size:1rem;color:#a0a0a0}#reviewscoreFilterList .filteritem-prefix,.hotel-list-container .ficon{margin-right:3px}.ssr-search-result .hotel-item-box{padding:0 180px 0 246px}.ssr-search-result-item .hotel-item-box{background-color:#f7f7f7;min-height:264px;transition:box-shadow .2s ease-in-out} .ssr-placeholder{top:60px}.top-filter-placeholder__cell:first-child{border-left:none}.list-filter-placeholder--sidebar .list-filter-placeholder__checkbox{margin:0}.list-filter-placeholder--sidebar .list-filter-placeholder__row{margin-left:3px;margin-bottom:11px}.box__fixed:first-child{margin-top:17px} .placeholder-text__title{font-size:18px}.placeholder__animation__supplier:nth-child(1){animation:supplier-1-slide-in 2s linear infinite}.placeholder__animation__supplier:nth-child(2){animation:supplier-2-slide-in 2s linear infinite}.placeholder__animation__supplier:nth-child(3){animation:supplier-3-slide-in 2s linear infinite}.placeholder__animation__supplier:nth-child(4){animation:supplier-4-slide-in 2s linear infinite}.placeholder__animation__supplier:nth-child(5){animation:supplier-5-slide-in 2s linear infinite}.placeholder__animation__supplier:nth-child(6){animation:supplier-6-slide-in 2s linear infinite}.hotel-item-box .property-review-container{padding-top:3px}.cor-container-display .crossout-line{color:#646464}.property-name-text-editor:not(:focus){border:1px solid #ccc}#searchPageLeftColumn .filter-list.filter-list-react ul,.filters-container--horizontal .filters-list{margin-bottom:0}.filter-box--horizontal:first-child{margin-left:-1px;width:calc(25% + 2px)}.filter-box--horizontal:last-child{margin-right:-1px}.filter-box--horizontal.text-filter{overflow:visible}.filter-box--horizontal .filter-menu{padding-top:6px;padding-right:0}.filter-box--horizontal .property-name{padding-top:0}.filter-box--horizontal .filter-items{padding-bottom:0}.filter-box--horizontal .filter-options{display:none}.filter-box--horizontal .filter-item-react{padding-top:0;padding-bottom:2px;margin-top:0}.filter-box--hoverable+.filter-box{border-left:1px solid #dcdbdc}.filter-box--horizontal.filter-box--hoverable{will-change:max-height;z-index:1;transition:all .15s linear}.filter-box:last-child{border-bottom:1px solid #e8e8e8}.sticky-filters .showMapBtns{margin-bottom:12px}.property-info-container .hotel-name{margin:0 10px 0 0}.pill-item.pill-item--label{position:relative;padding-left:8px;border-bottom-left-radius:2px;border-top-left-radius:2px;color:#fff;margin-right:0;border-left:none}.pill-item:nth-child(2){padding-left:8px;border-left:none}.pill-item:last-child{padding-right:8px;border-bottom-right-radius:2px;border-top-right-radius:2px}.pill-container.pill-green{color:#63c355}.pill-container.pill-orange{color:#f57c00}.urgency-messaging-container .people-looking{color:#ee595d;display:block}.urgency-messaging-container .ItemScroller{height:24px}.urgency-messaging-container .ItemScroller__item{width:365px;font-size:14px;text-overflow:ellipsis;white-space:nowrap;color:#555;overflow:hidden}@media screen and (max-width:1100px){.urgency-messaging-container .ItemScroller__item{width:240px}}.aps-peek-box .aps-peek-content{display:inline-block;background-color:#eee;border:.5px solid #ccc;border-radius:3px;padding:0 6px}.supplier-info-container .supplier-info{font-size:1rem;color:#a0a0a0}.hotel-price-container.reduced-line-height{line-height:1.7rem}.property-card .property-card-image{min-width:264px;min-height:194px}.property-card .thumbnail-item{float:left;width:82px;height:67px;border-right:3px solid #fff;overflow:hidden}.property-card .thumbnail-item-list{padding-top:3px}.ssr-search-result-item .property-card-content{box-shadow:rgba(0,0,0,.12) 0 2px 2px 0}.static-map-pin-blue.pin-blue-1{bottom:48%;left:27%}.static-map-pin-blue.pin-blue-2{bottom:59%;left:43%}.static-map-pin-blue.pin-blue-3{bottom:44%;left:53%}.static-map-pin-blue.pin-blue-4{bottom:59%;left:62%}.staticMapPanel .staticMapContainer{margin:0}.animateMapWrapper:not(.animated){overflow:hidden}.flatFilter__item:last-child{border-right:none}.main-header .logo img{width:89px;height:37px}li.navbar-menu-item.sign-in-up{padding-left:10px}html[dir=ltr] .l-row{margin-left:-16px}html[dir=ltr] [class*=l-col-]{float:left;padding-left:16px}html[dir=ltr] .SearchBoxTextDescription__child{right:15px}li.filter-box .range-slider-label{margin:0 8px 10px 2px}li.filter-box .rc-slider{margin-top:-8px}.SearchBoxContainer--compact .IconBox--checkOut::after,.SearchBoxContainer--compact .IconBox--checkOut::before,.SearchBoxContainer--compact .SearchBoxTextDescription--checkOut::after,.oneline-searchbox-compact.oneline-searchbox .dates.dates.checkin::after,.oneline-searchbox-compact.oneline-searchbox .dates.dates.checkout::after{content:none}.oneline-searchbox .oneline-search-button::before{content:"";position:absolute;transform:scale(0);height:30px;width:30px;top:0;left:0;bottom:0;right:0;opacity:0;background:#84b5fe;border-radius:100%;margin:auto;transition:transform .1s ease-out,opacity .1s ease-out,-webkit-transform .1s ease-out}.oneline-searchbox .dates i,.oneline-searchbox .occupancy i,.oneline-searchbox .search i{display:table-cell;padding:13px;font-weight:400}.oneline-searchbox .actions button{width:130px}@media screen and (max-width:1100px){.oneline-searchbox .actions button{width:100px}}.hotel-price-promo-container .rpn-container span{display:inline}body .ssr-container{margin-top:0}.sort-bar>a .icon-wrapper{position:absolute;top:17px;right:6px}.sort-bar>a.dropdown-wrapper{padding-right:20px}.sort-bar>a.selected{color:#fff;background-color:#5392f9}.sort-bar>a.sort-bar__header{width:10%;background-color:#f7f7f7;border-right:1px solid #ddd;padding:0 12px}@media (max-width:1024px) and (min-width:768px){.sort-bar_col-5>a.sort-bar__header{width:8%}.sort-bar_col-5>a:nth-child(2){width:10%}.sort-bar_col-5>a:nth-child(4){width:19%}.sort-bar_col-5>a:nth-child(6){width:21%}.sort-bar_col-5>a:nth-child(8){width:26%}.sort-bar_col-5>a:last-child{width:16%}}.sort-bar__toggle-link i:nth-child(1){color:#777}.sort-bar__toggle-link i:nth-child(2){color:#488bf8}.availability-message__percent span.book-percent-number{display:block;font-weight:700;line-height:20px;font-size:24px}.availability-message__percent span.book-percent-text{line-height:10px;font-size:10px;display:block}html[dir=ltr] .half-gauge__level--percentage-60{transform:rotate(108deg)}html[dir=ltr] .ficon-info-with-circle{margin-left:5px}.filter-box--horizontal .filter-items>ul{padding-top:0;padding-left:0}.pill-item.pill-item--label::after{position:absolute;top:0;right:-6px;width:0;height:0;content:"";border-right:6px solid transparent}a.staticMapPanel.showMapBtns{border:1px solid #d6d6d6}li.navbar-menu-item a.anchor{font-size:13px;font-weight:700;line-height:60px;min-width:44px;zoom:1;cursor:pointer;color:rgba(0,0,0,.75);padding:0}html[dir=ltr] .IconBox--checkOut::before{left:-5px}html[dir=ltr] .IconBox--checkOut::after{left:0}.sort-bar>a:last-of-type span{border-right:none}.navbar-menu-item>.language .flag,.oneline-searchbox-container #oneline-searchbox.oneline-searchbox .form-group.no-space{margin-right:0}#cxl_hover,#signin-popup.LOGIN-CSS .modal-signin.modal-signin-favorite .modal-container-right-content.hide,.LOGIN-CSS .popup-login .feedback-warning,.LOGIN-CSS .popup-login .respond-warning{display:none}.navbar-header .header-links-container .underline-link{width:0;bottom:0;opacity:0;z-index:1;position:absolute;border-bottom:4px solid #488bf8;margin:0;transition:all .2s ease-in-out}.Searchbox .IconBox--autocomplete .IconBox__child,.Searchbox .IconBox--checkIn .IconBox__child,.Searchbox .IconBox--checkOut .IconBox__child,.Searchbox .IconBox--occupancy .IconBox__child{vertical-align:middle}.oneline-searchbox .dates.checkout{border-left:0}.oneline-searchbox .dates .date{display:table-cell;width:100%;height:100%}.oneline-searchbox-compact.oneline-searchbox .dates{width:177px;box-shadow:none;padding:0 5px}.oneline-searchbox-compact.oneline-searchbox .occupancy,.oneline-searchbox-compact.oneline-searchbox .search{box-shadow:none;padding:0 5px}.property-info-container .areacity-name .ficon-pin-star{margin-bottom:2px}.hotel-price-promo-container .hotel-price-container .currency,.hotel-price-promo-container .hotel-price-container .price{font-size:2.2rem}.hotel-price-promo-container .hotel-price-container .currency{font-weight:300}.hotel-price-promo-container .hotel-price-container .dark-gray2{color:#a0a0a0}.hotel-price-promo-container .hotel-price-container .soft-red{color:#ee595d}.ssr-search-result-item .hotel-item-box .hotel-info{background-color:#f7f7f7;position:relative;min-height:264px;padding:7px 15px}.hotel-list-container .hotel-item-box .hotel-price-promo-container{width:180px;background-color:#fff;padding:0 8px 8px}.cor-container-display .price.dark-gray-text{color:#646464}.filter-box:not(.filter-box--emphasized):first-child{border-top:none}.aps-peek-box .aps-peek-content .aps-peek-icon{display:inline-block;color:#ccc;font-weight:700;margin:0 6px 5px 0}.aps-peek-box .aps-peek-content .aps-peek-price{padding-top:2px;font-size:20px;display:inline-block;text-align:right;color:#ccc;font-weight:700}.property-card .property-card-image .showcase-image{background-image:url(https://cdn5.agoda.net/images/mvc/default/bg-picture.png);background-position-x:center;background-position-y:center;background-repeat-x:no-repeat;background-repeat-y:no-repeat;background-color:#d6d6d6}.property-card .property-card-image .thumbnail-showcase{position:relative;width:246px;height:194px;overflow:hidden}.property-card .thumbnail-item:last-of-type{border-right:none}.property-card .thumbnail-item .thumbnail-image{position:relative;width:100%;height:100%;background-size:cover;background-repeat-x:no-repeat;background-repeat-y:no-repeat;background-position-x:50%;background-position-y:50%;background-color:#d6d6d6}.ssr-search-result-item .property-card-content .hotel-item-box{border:0}.animateMapWrapper.animating .static-map-pin-red{animation-timing-function:cubic-bezier(.5,0,.7,1);animation-duration:.3s;animation-name:pin-red-drop}.animateMapWrapper.animating .pin-blue-1{animation-name:pin-blue-drop-1;animation-delay:.4s}.animateMapWrapper.animating .pin-blue-2{animation-name:pin-blue-drop-2;animation-delay:.5s}.animateMapWrapper.animating .pin-blue-3{animation-name:pin-blue-drop-3;animation-delay:.1s}.animateMapWrapper.animating .pin-blue-4{animation-name:pin-blue-drop-4;animation-delay:.2s}.navbar-header .header-links-container li.header-links{position:static;border:0}.navbar-header .header-links-container li.header-links-active{font-weight:700}li.navbar-menu-item.sign-in-up .signin-up-combined{line-height:normal;margin:0;padding:0}@media only screen and (max-width:768px){.main-header .logo img.secondary{display:none}}html[dir=ltr] .SearchBoxTextDescription--occupancy .SearchBoxTextDescription__title{padding-right:34px}.filteritem-prefix i.ficon.star-orange{margin-right:-5px}.filter-item-react .filteritem-prefix i.ficon.star-orange,html[dir=ltr] .SearchBoxContainer--compact .IconBox--autocomplete{margin-left:0}.oneline-searchbox .dates.checkin::after{top:0;height:100%;position:absolute;right:0;z-index:2;border-right:1px solid #ccc}.oneline-searchbox .dates.checkout::after{position:absolute;left:-5px;top:0;width:5px;height:100%;z-index:1;background:#fff}.availability-message__percent span.book-percent-number:first-child{margin-bottom:2px}.pill-container.pill-green li:first-child{background-color:#63c355}.pill-container.pill-orange li:first-child{background-color:#f57c00}.property-card .thumbnail-item .thumbnail-image::after{content:"";position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;background:#000}.animateMapWrapper.animating img[class*=pin-blue-]{animation-timing-function:cubic-bezier(.5,0,.7,1);animation-duration:.3s}.pill-container.pill-green li:first-child::after{border-top:20px solid #63c355}.pill-container.pill-orange li:first-child::after{border-top:20px solid #f57c00}.ssr-search-result-item .hotel-item-box .hotel-info .urgency-messaging-container{}.property-card .property-card-image .thumbnail-showcase .showcase-image{position:absolute;top:0;width:100%;height:auto;min-height:194px;transition:opacity .2s ease-in}li.navbar-menu-item.sign-in-up .signin-up-combined.clear-min-width{min-width:initial}li.navbar-menu-item.sign-in-up .signin-up-combined .hollow{color:#488bf8;width:auto}li.navbar-menu-item.sign-in-up .signin-up-combined .solid{width:auto;border:1px solid #488bf8} .navbar-header .header-links-container li.header-links a.header-links-text{color:rgba(0,0,0,.75);padding:0 10px}li.navbar-menu-item.sign-in-up .signin-up-combined.clear-min-width>a{min-width:initial}li.navbar-menu-item.sign-in-up .signin-up-combined a.signin-text{margin-right:10px;margin-left:0;line-height:60px;font-size:13px;font-weight:600;text-transform:uppercase;border:0;padding:0}li.navbar-menu-item.sign-in-up .signin-up-combined a.signup-text{margin-left:10px;line-height:1;font-size:13px;font-weight:700;color:#488bf8;text-transform:uppercase;z-index:0;background:#fff;overflow:hidden;padding:13px 7px;transition:background .2s ease-in}li.navbar-menu-item.sign-in-up .signin-up-combined a.signup-text::before{content:"";position:absolute;color:#488bf8;transform:scale(0);height:40px;width:40px;top:0;left:0;bottom:0;right:0;opacity:0;animation:unripple .3s ease-out reverse;background:#488bf8;border-radius:100%;margin:auto;transition:opacity .3s ease-in}li.navbar-menu-item.sign-in-up .signin-up-combined a.signup-text span{position:relative;transition:color .2s ease-in}.desktop.chrome .property-card .thumbnail-item .thumbnail-image{transform:rotate(.001deg);transition:all .3s ease-in-out}@media (max-width:1100px){#header-taxi-menu-item{display:none}}#PriceFilterRange,#joinedFilterList{position:relative}#joinedFilterList .filter-list{overflow:inherit}#filterNameList .filter-items{position:relative;padding-bottom:20px;overflow:inherit}#filterNameList .property-name-input-box{padding-right:5px;overflow:inherit}#transportationFilterList .filteritem-prefix i{margin-right:0;font-size:1.6rem}.oneline-searchbox-container #oneline-searchbox.oneline-searchbox{width:auto;height:auto;z-index:3;background:0 0;margin:0 auto;padding:11px 0}#searchPageLeftColumn .filter-list.filter-list-react,.oneline-searchbox-container #oneline-searchbox.oneline-searchbox>dl{margin:0}.oneline-searchbox-container #oneline-searchbox.oneline-searchbox .form-group{margin-bottom:0;margin-right:5px}.oneline-searchbox-container #oneline-searchbox.oneline-searchbox .oneline-search-button{font-size:14px;border-radius:3px}@media screen and (max-width:1100px),(-webkit-min-device-pixel-ratio:0){.oneline-searchbox-container #oneline-searchbox.oneline-searchbox .oneline-search-button{width:90px}}@media screen and (min-width:1100px){.oneline-searchbox-container #oneline-searchbox.oneline-searchbox .oneline-search-button{width:130px}}.oneline-searchbox-container #oneline-searchbox.oneline-searchbox>dl:last-of-type{margin-right:0}.oneline-searchbox-container #oneline-searchbox.oneline-searchbox>dl:first-of-type{margin-left:0}#searchPageReactRoot{min-height:100vh;}
    .sticky-filters .showMapBtns{margin-bottom:unset}
    .ficon{display:none}

    /* Mobile Responsive Overrides */
    @media (max-width: 991.98px) {
        body .container-agoda, .container-agoda {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        #searchPageLeftColumn {
            display: none !important;
        }
        #searchPageRightColumn {
            width: 100% !important;
            max-width: 100% !important;
            float: none !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .main-column, .l-row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }
        .Header__LogoAndLinks__Links,
        .Header__Actions__ListYourPlace,
        .Header__Actions__CreateAccount,
        #page-header a[href*="register"],
        #page-header button[data-element-name="header-list-your-place-button"] {
            display: none !important;
        }
        #page-header .Headerstyled__HeaderSection-sc-vtperc-0 {
            padding: 0 10px !important;
        }
        #topbar,
        #topbar .topbar-wrapper,
        #topbar .Searchbox,
        #topbar .SearchBoxContainer,
        #topbar .SearchBoxContainer--compact,
        #topbar .SearchBoxContainer--compact .Searchbox {
            max-height: none !important;
            height: auto !important;
            min-height: auto !important;
            padding-bottom: 12px !important;
        }
        #topbar .SearchBoxContainer,
        #topbar .Searchbox__wrapper {
            width: 100% !important;
        }
        #topbar .abb64-flex-row {
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        #topbar .IconBox--autocomplete {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 6px !important;
        }
        #topbar .IconBox--checkIn {
            width: calc(50% - 3px) !important;
            margin-right: 0 !important;
            margin-bottom: 6px !important;
        }
        #topbar .IconBox--checkOut {
            width: calc(50% - 3px) !important;
            margin-right: 0 !important;
            margin-bottom: 6px !important;
        }
        #topbar .IconBox--occupancy {
            width: 100% !important;
            margin-right: 0 !important;
            margin-bottom: 6px !important;
        }
        #topbar .abb64-inline-flex {
            width: 100% !important;
        }
        #topbar button[data-selenium="searchButton"] {
            width: 100% !important;
        }
        #sort-bar > div {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        #sort-bar > div > div:last-child {
            width: 100% !important;
        }
        .hotel-list-container {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
        #sideColumnInfo, .redirectToApp, #fullFunnelBannerTarget {
            display: none !important;
        }
    }
</style>

<style>.ssr-placeholder {top: 60px } .theme-citius .ssr-placeholder {top: 0} .ssr-placeholder--hidden {opacity: 0} .ssr-placeholder .oneline-searchbox-container {margin-bottom: 0 }</style>

<style>.c-btn,.c-checkbox,.c-icon{display:inline-block}.c-btn{height:40px;padding:0 40px;border:1px solid transparent;border-radius:3px;font-size:14px;font-weight:700;line-height:38px;vertical-align:middle;outline:0;white-space:nowrap;user-select:none;text-align:center;text-decoration:none;transition:all .3s ease}.hidden{display:none!important;visibility:hidden!important}.LogoAndLinksMenu__LogoContainer{display:inline-block;position:relative;line-height:60px}.LogoAndLinksMenu__AgodaLink{display:inline-block}.LogoAndLinksMenu__AgodaLogo{width:89px;height:37px}.LogoAndLinksMenu__AgodaSecondaryLogo{display:none}.LinkContainer,.LinkContainer__Link,.LogoAndLinksMenu__AgodaSecondaryLogo--loaded{display:inline-block}html[dir=ltr] .LogoAndLinksMenu__AgodaSecondaryLogo{margin-left:13px}html[dir=rtl] .LogoAndLinksMenu__AgodaSecondaryLogo{margin-right:13px}.LogoAndLinksMenu__PartnerLogo{margin-left:11px}.LinkContainer{position:relative;margin-bottom:0}html[dir=ltr] .LinkContainer{margin-left:30px}html[dir=rtl] .LinkContainer{margin-right:30px}.LinkContainer::before{position:absolute;bottom:10px;top:10px;border-right:1px solid #eee;content:' '}html[dir=ltr] .LinkContainer::before{left:-10px}html[dir=rtl] .LinkContainer::before{right:-10px}.LinkContainer__LinkWrapper{display:inline-block;cursor:pointer}.LinkContainer__Link{padding:0 10px;font-weight:700;line-height:60px;min-width:44px}.LinkContainer__Link:focus,.LinkContainer__Link:link,.LinkContainer__Link:visited{color:rgba(0,0,0,.75);text-decoration:none}.LinkContainer__Link:hover{color:#488BF8;text-decoration:none}.LinkContainer__Highlight{position:absolute;margin:0;bottom:0;border-bottom:4px solid #488BF8;transition:all .2s ease-in-out;z-index:-1}.header-container{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;padding:0 16px}.header-container__item--left{-webkit-box-flex:0;-webkit-flex:none;-ms-flex:none;flex:none}.header-container__item--right{-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;display:-webkit-inline-box;display:-webkit-inline-flex;display:-ms-inline-flexbox;display:inline-flex;-webkit-box-pack:end;-webkit-justify-content:flex-end;-ms-flex-pack:end;justify-content:flex-end;position:relative}.header-container__item--right a{color:#333;text-decoration:none}.header-container__space{-webkit-box-flex:0;-webkit-flex:none;-ms-flex:none;flex:none;width:130px}.header-dropdown>*{display:inline-block;vertical-align:middle;margin:0 4px;line-height:60px}.header-dropdown__user-icon-container{position:relative}.header-container__item--logged-in .header-dropdown__user-icon-container{min-width:50px}.header-dropdown__fb-image{width:40px;height:40px;border-radius:20px}.header-dropdown__user-icon{font-size:1.7rem;font-weight:700;font-style:normal;width:30px;height:30px;line-height:30px}[class*=bg-user-icon-].header-dropdown__user-icon{display:inline-block}html[dir=ltr] .header-dropdown__user-icon--noti{right:-5px}html[dir=rtl] .header-dropdown__user-icon--noti{left:-5px}i.header-dropdown__user-icon--noti{float:initial;line-height:30px;margin:initial}i.header-dropdown__user-icon--noti span{padding:initial}.header-dropdown__user-icon--fb{position:relative;width:40px;height:40px;display:inline-block}.header-dropdown__notification-badge{display:inline-block;min-width:15px;padding:1px 3px;position:relative;top:-15px;color:#fff;text-align:center;border:1px solid #fff;line-height:11px;font-size:10px;background-color:#E44746;border-radius:10px}html[dir=ltr] .header-dropdown__notification-badge{right:5px}html[dir=rtl] .header-dropdown__notification-badge{left:5px}.header-dropdown__giftcard{color:#d68100}.header-dropdown__giftcard i{margin:0 4px}.header-dropdown__hamburger{font-weight:700;margin-bottom:0;padding:0 10px;line-height:60px}.header-menu__subgroup-container--lc-panel{box-shadow:0 4px 10px 0 rgba(0,0,0,.12);padding:0;background:#fff;border:1px solid #ccc;top:64px;z-index:1000;width:685px}.ReactModalPortal .header-menu__subgroup-container--lc-panel{max-height:85vh;overflow-y:auto}.header-menu{margin:0}.header-menu__subgroup-container--lc-panel .header-menu__subgroup{padding:8px 16px;color:#333;line-height:1.9rem}.header-menu__subgroup-container--lc-panel .header-menu__item{display:inline-block;width:33.33333%;line-height:1.9rem;padding:0;border-bottom:1px solid #eee}.header-menu__subgroup-container--lc-panel .header-menu__item.selected a{background-color:#f8f8f8}.header-menu__subgroup-container--lc-panel .header-menu__item a{display:inline-block;line-height:1.9rem;list-style:none;text-decoration:none;font-size:12px;padding:9px 12px}.header-menu__subgroup-container--lc-panel .header-menu__item a:hover{color:#488BF8;background-color:#f8f8f8}html[dir=ltr] .header-menu--master .header-menu__subgroup-container--lc-panel::before,html[dir=rtl] .header-menu--master .header-menu__subgroup-container--lc-panel::before{content:'';width:10px;height:10px;background-color:#f5f5f5;border-left:1px solid #ccc;border-top:1px solid #ccc;position:absolute;top:-4px;display:block}.header-menu__subgroup-container--lc-panel .header-menu__item--last-row{border-bottom:none}html[dir=ltr] .header-menu--master .header-menu__subgroup-container--lc-panel::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);right:175px}html[dir=rtl] .header-menu--master .header-menu__subgroup-container--lc-panel::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);left:175px}.header__loading-panel,.loading-panel--qrcode{box-shadow:0 4px 10px 0 rgba(0,0,0,.12);top:64px;z-index:1000}.header-menu__subgroup-container--lc-panel .header-menu__group{display:block}.header-menu__subgroup-container--lc-panel .header-menu__item a{width:95%}.ReactModal__Content--header-modal .header-menu__subgroup-container--lc-panel .header-menu__item a{color:#333;text-decoration:none}.header__loading-panel{padding:0;background:#fff;border:1px solid #ccc;width:685px;height:290px}html[dir=ltr] .header-menu--master .header__loading-panel::before,html[dir=rtl] .header-menu--master .header__loading-panel::before{content:'';width:10px;height:10px;background-color:#fff;border-left:1px solid #ccc;border-top:1px solid #ccc;display:block;top:-4px;position:absolute}.header__loading-panel .header-menu__subgroup{padding:8px 16px;color:#333;line-height:1.9rem}html[dir=ltr] .header-menu--master .header__loading-panel::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);right:175px}html[dir=rtl] .header-menu--master .header__loading-panel::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);left:175px}.header__loading-panel .brand-agoda-loader{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%)}.language-trigger{display:inline-block}.header-menu--master .language-trigger__icon.flag-18{margin:0}html[dir=ltr] .header-menu--slave .currency-trigger__text,html[dir=ltr] .header-menu--slave .language-trigger__icon,html[dir=ltr] .header-menu__subgroup-container--currency .header-menu__item .currency__text{margin-right:15px}html[dir=rtl] .header-menu--slave .currency-trigger__text,html[dir=rtl] .header-menu--slave .language-trigger__icon,html[dir=rtl] .header-menu__subgroup-container--currency .header-menu__item .currency__text{margin-left:15px}.header-menu--master .header-menu__subgroup-container--lc-group>.header-menu__group-header,.header-menu--master .language-trigger__text{display:none}.currency-trigger{display:inline-block}.header-menu--slave .currency-trigger__text{width:24px;text-align:center;display:inline-block}.currency-trigger__secondary-text{text-overflow:ellipsis;display:inline-block;max-width:150px;white-space:nowrap;overflow:hidden;vertical-align:bottom}.header-menu--master .currency-trigger__secondary-text,.header-menu--master .header-menu__subgroup-container--currency .currency-overlay__secondary-text{display:none}.header-menu__subgroup-container--currency .header-menu__item .currency__text{color:#488BF8;width:13.5%;display:inline-block}.header-menu__slave::before{content:'';border-left:1px solid #EEE;margin:10px;vertical-align:top}.header-menu::before{content:'';display:inline-block;width:1px;height:100%}.header-menu--master{height:60px;overflow:hidden}.header-menu--master.header-menu--reverse-order > li:not(:first-child)::after{content:'';display:inline-block;border-left:1px solid #EEEEEE;height:40px;vertical-align:top;margin:10px}.header-menu--master.header-menu--reverse-order > li:not(:first-child) > .header-menu__subgroup-container{display:inline-block}html[dir=ltr] .header-menu--master.header-menu--reverse-order > .header-menu__item,html[dir=ltr] .header-menu--master.header-menu--reverse-order > .header-menu__group,html[dir=ltr] .header-menu--master.header-menu--reverse-order::before{float:right}html[dir=rtl] .header-menu--master.header-menu--reverse-order > .header-menu__item,html[dir=rtl] .header-menu--master.header-menu--reverse-order > .header-menu__group,html[dir=rtl] .header-menu--master.header-menu--reverse-order::before{float:left}.header-menu--master.header-menu--ascending-order{min-width:100px}html[dir=ltr] .header-menu--master.header-menu--ascending-order{text-align:right}html[dir=rtl] .header-menu--master.header-menu--ascending-order{text-align:left}.header-container__item--logged-in .header-menu--master.header-menu--ascending-order > li::after{content:'';display:inline-block;border-left:1px solid #EEEEEE;height:40px;vertical-align:top;margin:10px}.header-container__item--logged-in .header-menu--master.header-menu--ascending-order > li > .header-menu__subgroup-container{display:inline-block}.header-container__item--not-log-in .header-menu--master.header-menu--ascending-order > li:not(:last-child)::after{content:'';display:inline-block;border-left:1px solid #EEEEEE;height:40px;vertical-align:top;margin:10px}.header-container__item--not-log-in .header-menu--master.header-menu--ascending-order > li:not(:last-child) > .header-menu__subgroup-container{display:inline-block}.header-menu--master .header-menu__group{vertical-align:top}.header-menu--master .header-menu__item{padding:0 10px;line-height:60px}.header-menu--master .Popup{position:absolute;z-index:1000}.header-menu__group,.header-menu__item,.header-menu__slave{display:inline-block}.header-menu__group-header{padding:14px 26px;height:40px;text-transform:uppercase;color:gray;background:#f5f5f5;font-size:90%;display:block;font-weight:400;line-height:1.1}.header-menu__slave{vertical-align:top;-webkit-box-flex:0;-webkit-flex:none;-ms-flex:none;flex:none;cursor:pointer}.header-menu__slave::before{display:inline-block;height:40px}.header-menu__slave>*{display:inline-block}.header-menu__slave--hide-separator::before{border-color:transparent}.header-menu__slave--without-separator::before{content:none}.header-menu__slave .header-menu__group{display:block}.header-menu__slave .header-menu__item{display:block;line-height:1.43}.header-menu__slave .header-menu__item a{display:block;padding:10px 0}.header-menu__subgroup-container ul.radio-group{display:table}.header-menu__subgroup-container li.radio-option{display:table-cell;height:100%;vertical-align:top;border-left:8px solid transparent}html[dir=rtl] .header-menu__subgroup-container li.radio-option{border-left:0;border-right:8px solid transparent}.header-menu__subgroup-container li.radio-option.selected{background-color:#f8f8f8}.ReactModal__Content--header-modal{-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);position:absolute}.ReactModal__close-button{display:block;margin-bottom:15px;color:#ccc;font-weight:600;cursor:pointer}html[dir=ltr] .ReactModal__close-button{text-align:right}html[dir=rtl] .ReactModal__close-button{text-align:left}.header-menu__subgroup-container--lc-group .Popup__content{position:static}.header-menu--master .qrcode-trigger__icon{margin:0}html[dir=ltr] .header-menu--slave .qrcode-trigger__icon{margin-right:15px}html[dir=rtl] .header-menu--slave .qrcode-trigger__icon{margin-left:15px}.header-menu--master .header-menu__subgroup-container--cn-group>.header-menu__group-header,.header-menu--master .qrcode-trigger__text{display:none}.loading-panel--qrcode{background:#fff;border:1px solid #ccc;width:140px;height:188px;line-height:10px;padding:10px;text-align:center}html[dir=ltr] .header-menu--master .loading-panel--qrcode::before,html[dir=rtl] .header-menu--master .loading-panel--qrcode::before{content:'';width:10px;height:10px;background-color:#fff;border-left:1px solid #ccc;border-top:1px solid #ccc;display:block;position:absolute;top:-4px}.header-menu--master .header-menu__subgroup-container--login-group .header-menu__group-header{display:none}.header-menu--master .header-menu__subgroup-container--login-group .signup-button{padding:0 7px}.header-menu--master .header-menu__subgroup-container--login-group .signin-button{padding:0}.signin-button,.signout-button,.signup-button{text-transform:uppercase;vertical-align:unset}.header-menu--slave .signin-button,.header-menu--slave .signout-button,.header-menu--slave .signup-button{width:100%;margin:5px 0}.c-btn--outline-primary{border-color:#5392f9;background-color:#fff;color:#5392f9}.signin-button.c-btn--outline-primary,.signout-button.c-btn--outline-primary,.signup-button.c-btn--outline-primary{position:relative;overflow:hidden;z-index:0}.signin-button.c-btn--outline-primary>*,.signout-button.c-btn--outline-primary>*,.signup-button.c-btn--outline-primary>*{position:relative}.signin-button.c-btn--transparent,.signout-button.c-btn--transparent,.signup-button.c-btn--transparent{border:none;background:0 0;color:#488bf8}.loading-panel--qrcode .header-menu__subgroup{padding:8px 16px;color:#333;line-height:1.9rem}html[dir=ltr] .header-menu--master .loading-panel--qrcode::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);right:70px}html[dir=rtl] .header-menu--master .loading-panel--qrcode::before{-webkit-transform:rotate(45deg);transform:rotate(45deg);left:70px}.loading-panel--qrcode .description{padding:10px 0;line-height:18px;font-weight:700;font-size:12px}.loading-panel--qrcode .wechat-qrcode-hd{width:100%;height:100%;background-size:contain;background-repeat:no-repeat;background-image:url(//cdn6.agoda.net/images/CW-271/default/qrcode-wechat-v1.png)}.loading-panel--qrcode .app-download-qrcode-hd{width:100%;height:100%;background-size:contain;background-repeat:no-repeat;background-image:url(//cdn6.agoda.net/images/CW-7/default/qrcode-agoda.png)}.theme-japanican .LogoAndLinksMenu__Logo,.theme-japanican .LogoAndLinksMenu__AgodaLogo {width: 210px;height: 23px;}html[lang=cn] .theme-japanican .LogoAndLinksMenu__Logo,html[lang=cn] .theme-japanican .LogoAndLinksMenu__AgodaLogo {width: 89px;height: 36px;}html[lang=tw] .theme-japanican .LogoAndLinksMenu__Logo,html[lang=tw] .theme-japanican .LogoAndLinksMenu__AgodaLogo {width: 107px;height: 36px;}html[lang=kr] .theme-japanican .LogoAndLinksMenu__Logo,html[lang=kr] .theme-japanican .LogoAndLinksMenu__AgodaLogo {width: 111px;height: 36px;}html[lang=kr] .theme-japanican .LogoAndLinksMenu__Logo,html[lang=kr] .theme-japanican .LogoAndLinksMenu__AgodaLogo {width: 111px;height: 36px;} .theme-rurubu .LogoAndLinksMenu__Logo,.theme-rurubu .LogoAndLinksMenu__AgodaLogo {width: 190px;height: 36px;},.theme-clubtravel .LogoAndLinksMenu__AgodaLogo {width: 192px;height: 40px;},.theme-lottedfs .LogoAndLinksMenu__AgodaLogo {width: 35px;height: 32px;}</style>

<style>.travel-with-kids-container{display:none;}</style>

<style>.full-funnel-placeholder{position:relative;height:60px;width:100%;background-color:#f7f7f7;display:inline-block;} .full-funnel-banner{display:none}</style>

<style>.tab-filter-placeholder::after,.tab-filter-placeholder::before,.tab-filter-placeholder__item:not(.tab-filter-placeholder__item--selected){border-bottom:1px solid #dddfe2}html[dir=ltr] .tab-filter-placeholder::after,html[dir=ltr] .tab-filter-placeholder__item{border-left:1px solid #dddfe2}html[dir=rtl] .tab-filter-placeholder::after,html[dir=rtl] .tab-filter-placeholder__item{border-right:1px solid #dddfe2}.tab-filter-placeholder{display:flex;height:56px;margin:30px 0}.ie9 .tab-filter-placeholder{display:table}.tab-filter-placeholder__item{width:220px;border-top:1px solid #dddfe2;display:flex;align-items:center;justify-content:center}.ie9 .tab-filter-placeholder__item{display:table-cell;text-align:center;vertical-align:middle}.tab-filter-placeholder__item--selected{border-top-width:5px}.tab-filter-placeholder__title{width:70px;height:14px;background:#e9ebee}.ie9 .tab-filter-placeholder__title{margin:auto}.tab-filter-placeholder::before{content:'';min-width:10px}.tab-filter-placeholder::after{content:'';flex-grow:1}.ie9 .tab-filter-placeholder::after{width:100%} </style>

<style type="text/css">.Searchbox .IconBox{ border-radius: 6px; } .Searchbox .IconBox--checkIn{border-radius: 6px 0 0 6px; margin: 0; } html[dir=rtl] .Searchbox .IconBox--checkIn{border-radius: 0 6px 6px 0; margin: 0;} .Searchbox .IconBox--checkOut{border-radius: 0 6px 6px 0; margin: 0;} html[dir=rtl] .Searchbox .IconBox--checkOut{border-radius: 6px 0 0 6px; margin: 0 1px 0 0} .Searchbox .IconBox--autocomplete{ margin: 0 3px;} .Searchbox .IconBox--occupancy{margin: 0 3px;}</style>

<style type="text/css">.side-filter-container-placeholder {margin-top: 20px;border: 1px solid #dcdbdc;} .side-filter-container-placeholder .side-filter-title-placeholder {padding: 13px;border-bottom: 1px solid #dcdbdc;} .side-filter-container-placeholder .side-filter-title-placeholder__text {width: 70%;height: 24px;background-color: #e9ebee;} .side-filter-container-placeholder .side-filter-cell-placeholder {padding: 20px 14px 0 14px;} .side-filter-container-placeholder .placeholder-title-header--sidebar {margin-bottom: 17px;} .side-filter-container-placeholder .list-filter-placeholder--sidebar {padding: 0 0 8px 0;}</style>


    <link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1444-76aee892951d.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1297-da94764ae787.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4754-c334e52610a7.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/7311-188a0c4d0179.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5132-8448d60da369.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/8140-85725b62765c.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/9565-effe454bf681.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/3852-dd14c62e38a3.js" as="script"><link rel="preload" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/search-9a09eeb2f114.js" as="script">


    <link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1297-81103bc3ecd4.css" rel="stylesheet" onerror="loadErrorHandler.call(this, event)"><link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5132-7d6f3e76125d.css" rel="stylesheet" onerror="loadErrorHandler.call(this, event)"><link href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/search-d7c52cdf705c.css" rel="stylesheet" onerror="loadErrorHandler.call(this, event)">


    <link rel="preconnect" href="https://cdn6.agoda.net">
    <link rel="preconnect" href="//pix6.agoda.net">


    







            
        
<link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1467-2ed4d1bb3553.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/dynamic-search-bundle-coreBundle-4048fc571f65.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/6522-d51775bfe82d.css"><style data-styled="active" data-styled-version="5.3.11"></style><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2253-804a78cf4607.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/7487-0447de72b94b.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2911-78b9810a6dd7.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5004-1036beaf8ac3.css"><style data-kite-js="" data-kite-js-version-core="0.0.1"></style><style type="text/css">.ConsentBanner {
  bottom: 0;
  position: fixed;
  z-index: 1010;
  padding: 13px;
  background: #F0F3F9;
  box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.2);
  border-radius: 5px;
  display: flex;
  justify-content: space-between; }
  @media (max-width: 768px) {
    .ConsentBanner {
      display: block;
      padding: 10px 16px;
      background: #F7F9FC; } }
  .ConsentBanner img {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: block; }
  .ConsentBanner .Text {
    color: #24262C;
    width: 77%; }
    @media (max-width: 768px) {
      .ConsentBanner .Text {
        width: auto; } }
    .ConsentBanner .Text__Title {
      font-weight: 500;
      font-size: 16px;
      line-height: 25px; }
    .ConsentBanner .Text__Description {
      font-weight: 376;
      font-size: 14px;
      line-height: 16px; }
  .ConsentBanner .BtnContainer {
    position: relative;
    height: 35px;
    margin: auto;
    display: flex; }
    html[dir=rtl] .ConsentBanner .BtnContainer {
      order: 0; }
    @media (max-width: 768px) {
      .ConsentBanner .BtnContainer {
        height: 6vh;
        padding: 9px 0px;
        margin: unset; } }
    .ConsentBanner .BtnContainer .CookieSettingBtn {
      height: 30px;
      color: #3E6CEA;
      font-weight: 400;
      border: none;
      background: none;
      margin: auto 18px; }
      @media (max-width: 768px) {
        .ConsentBanner .BtnContainer .CookieSettingBtn {
          position: absolute;
          margin: auto; } }
    .ConsentBanner .BtnContainer .BtnPair {
      display: flex;
      flex-direction: column-reverse;
      margin: auto;
      transform: translateY(-16px); }
      @media (max-width: 768px) {
        .ConsentBanner .BtnContainer .BtnPair {
          position: absolute;
          right: 0;
          transform: none;
          display: block; }
          html[dir=rtl] .ConsentBanner .BtnContainer .BtnPair {
            left: 0;
            right: auto; } }
      .ConsentBanner .BtnContainer .BtnPair__RejectBtn {
        background: #FFFFFF;
        border: 1px solid #3E6CEA;
        border-radius: 16px;
        width: 84px;
        height: 30px;
        color: #3E6CEA;
        font-weight: 500; }
        @media (max-width: 768px) {
          .ConsentBanner .BtnContainer .BtnPair__RejectBtn {
            margin-right: 7px; } }
        html[dir=rtl] .ConsentBanner .BtnContainer .BtnPair__RejectBtn {
          float: left;
          margin-left: 7px; }
      .ConsentBanner .BtnContainer .BtnPair__AcceptBtn {
        width: 84px;
        height: 30px;
        background: #3E6CEA;
        border-radius: 16px;
        margin-bottom: 6px;
        color: #FFFFFF;
        border: 1px solid #3E6CEA;
        font-weight: 500; }
</style><style type="text/css">.ConsentBannerFunctionalOnly {
  bottom: 0;
  position: fixed;
  z-index: 1010;
  padding: 13px;
  background: #F0F3F9;
  box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.2);
  border-radius: 5px;
  display: flex;
  justify-content: space-between;
  width: 100%; }
  @media (max-width: 768px) {
    .ConsentBannerFunctionalOnly {
      display: block;
      padding: 10px 16px;
      background: #F7F9FC; } }
  .ConsentBannerFunctionalOnly img {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: block; }
  .ConsentBannerFunctionalOnly .Text {
    color: #24262C;
    width: 77%; }
    @media (max-width: 768px) {
      .ConsentBannerFunctionalOnly .Text {
        width: auto; } }
    .ConsentBannerFunctionalOnly .Text__Title {
      font-weight: 500;
      font-size: 16px;
      line-height: 25px; }
    .ConsentBannerFunctionalOnly .Text__Description {
      font-weight: 376;
      font-size: 14px;
      line-height: 16px; }
  .ConsentBannerFunctionalOnly .BtnContainer {
    position: relative;
    display: flex;
    justify-content: flex-end;
    align-items: center; }
    html[dir=rtl] .ConsentBannerFunctionalOnly .BtnContainer {
      order: 0; }
    @media (max-width: 768px) {
      .ConsentBannerFunctionalOnly .BtnContainer {
        height: 6vh;
        padding: 9px 0px;
        margin: unset;
        display: flex;
        justify-content: flex-end; } }
    .ConsentBannerFunctionalOnly .BtnContainer .BtnPair {
      display: flex;
      flex-direction: column-reverse;
      margin: auto; }
      @media (max-width: 768px) {
        .ConsentBannerFunctionalOnly .BtnContainer .BtnPair {
          position: absolute;
          right: 0;
          transform: none;
          display: block; }
          html[dir=rtl] .ConsentBannerFunctionalOnly .BtnContainer .BtnPair {
            left: 0;
            right: auto; } }
      .ConsentBannerFunctionalOnly .BtnContainer .BtnPair__RejectBtn {
        background: #FFFFFF;
        border: 1px solid #3E6CEA;
        border-radius: 16px;
        width: 84px;
        height: 30px;
        color: #3E6CEA;
        font-weight: 500; }
        @media (max-width: 768px) {
          .ConsentBannerFunctionalOnly .BtnContainer .BtnPair__RejectBtn {
            margin-right: 7px; } }
        html[dir=rtl] .ConsentBannerFunctionalOnly .BtnContainer .BtnPair__RejectBtn {
          float: left;
          margin-left: 7px; }
</style><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2483-e3942a16cf2e.css"><style id="googleidentityservice_button_styles">.qJTHM{-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;color:#202124;direction:ltr;-webkit-touch-callout:none;font-family:Roboto-Regular,arial,sans-serif;-webkit-font-smoothing:antialiased;font-weight:400;margin:0;overflow:hidden;-webkit-text-size-adjust:100%}.ynRLnc{left:-9999px;position:absolute;top:-9999px}.L6cTce{display:none}.bltWBb{overflow-wrap:break-word;word-break:break-word}.hSRGPd{color:#1a73e8;cursor:pointer;font-weight:500;text-decoration:none}.Bz112c-W3lGp{height:16px;width:16px}.Bz112c-E3DyYd{height:20px;width:20px}.Bz112c-r9oPif{height:24px;width:24px}.Bz112c-u2z5K{height:36px;width:36px}.Bz112c-uaxL4e{border-radius:10px}.LgbsSe-Bz112c{display:block}.S9gUrf-YoZ4jf{border:none;margin:0;padding:0}.S9gUrf-YoZ4jf *{border:none;margin:0;padding:0}.fFW7wc-ibnC6b>.aZ2wEe>div{border-color:#4285f4}.P1ekSe-ZMv3u{-webkit-transition:height .2s linear;transition:height .2s linear}.P1ekSe-ZMv3u>div:first-child{background-color:#1a73e8!important;-webkit-transition:width .3s linear;transition:width .3s linear}.P1ekSe-ZMv3u>div:nth-child(2){background-image:-webkit-gradient(linear,left top,right top,from(rgba(255,255,255,.7)),to(rgba(255,255,255,.7))),-webkit-gradient(linear,left top,right top,from(#1a73e8),to(#1a73e8))!important;background-image:-webkit-linear-gradient(left,rgba(255,255,255,.7),rgba(255,255,255,.7)),-webkit-linear-gradient(left,#1a73e8,#1a73e8)!important;background-image:linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.7)),linear-gradient(90deg,#1a73e8,#1a73e8)!important}.P1ekSe-ZMv3u>div:nth-child(3){background-image:-webkit-gradient(linear,left top,right top,from(rgba(255,255,255,.7)),to(rgba(255,255,255,.7))),-webkit-gradient(linear,left top,right top,from(#1a73e8),to(#1a73e8))!important;background-image:-webkit-linear-gradient(left,rgba(255,255,255,.7),rgba(255,255,255,.7)),-webkit-linear-gradient(left,#1a73e8,#1a73e8)!important;background-image:linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.7)),linear-gradient(90deg,#1a73e8,#1a73e8)!important}.haAclf{display:inline-block}.nsm7Bb-HzV7m-LgbsSe{border-radius:4px;box-sizing:border-box;-webkit-transition:background-color .218s,border-color .218s;transition:background-color .218s,border-color .218s;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;-webkit-appearance:none;background-color:#fff;background-image:none;border:1px solid #dadce0;color:#3c4043;cursor:pointer;font-family:Google Sans,arial,sans-serif;font-size:.875rem;height:40px;letter-spacing:.25px;outline:none;overflow:hidden;padding:0 12px;position:relative;text-align:center;vertical-align:middle;white-space:nowrap;width:auto}.nsm7Bb-HzV7m-LgbsSe.TzA9Ye-LgbsSe{font-size:14px}@media screen and (-ms-high-contrast:active){.nsm7Bb-HzV7m-LgbsSe{border:2px solid windowText;color:windowText}}@media screen and (preferes-contrast:more){.nsm7Bb-HzV7m-LgbsSe{color:#000}}.nsm7Bb-HzV7m-LgbsSe.pSzOP-SxQuSe{font-size:.875rem;height:32px;letter-spacing:.25px;padding:0 10px}.nsm7Bb-HzV7m-LgbsSe.pSzOP-SxQuSe.TzA9Ye-LgbsSe{font-size:14px}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe{font-size:.6875rem;height:20px;letter-spacing:.3px;padding:0 8px}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe.TzA9Ye-LgbsSe{font-size:11px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe{padding:0;width:40px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe.pSzOP-SxQuSe{width:32px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe.purZT-SxQuSe{width:20px}.nsm7Bb-HzV7m-LgbsSe.JGcpL-RbRzK{border-radius:20px}.nsm7Bb-HzV7m-LgbsSe.JGcpL-RbRzK.pSzOP-SxQuSe{border-radius:16px}.nsm7Bb-HzV7m-LgbsSe.JGcpL-RbRzK.purZT-SxQuSe{border-radius:10px}.nsm7Bb-HzV7m-LgbsSe.i5vt6e-to915-Ia7Qfc{border:1px solid #8e918f;background-color:#131314;color:#e3e3e3}.nsm7Bb-HzV7m-LgbsSe.MFS4be-Ia7Qfc{border:none;color:#fff}.nsm7Bb-HzV7m-LgbsSe.MFS4be-v3pZbf-Ia7Qfc{background-color:#1a73e8}.nsm7Bb-HzV7m-LgbsSe.MFS4be-JaPV2b-Ia7Qfc{background-color:#202124;color:#e8eaed}@media screen and (prefers-contrast:more){.nsm7Bb-HzV7m-LgbsSe.MFS4be-JaPV2b-Ia7Qfc{color:#fff}}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{height:18px;margin-right:8px;min-width:18px;width:18px}.nsm7Bb-HzV7m-LgbsSe.pSzOP-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{height:14px;min-width:14px;width:14px}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{height:10px;min-width:10px;width:10px}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-Bz112c{margin-left:8px;margin-right:-4px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{margin:0;padding:10px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe.pSzOP-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{padding:8px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe.purZT-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c{padding:4px}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-top-left-radius:3px;border-bottom-left-radius:3px;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;background-color:#fff;height:36px;margin-left:-10px;margin-right:12px;min-width:36px;width:36px}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf .nsm7Bb-HzV7m-LgbsSe-Bz112c,.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf .nsm7Bb-HzV7m-LgbsSe-Bz112c{margin:0;padding:0}.nsm7Bb-HzV7m-LgbsSe.pSzOP-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{height:28px;margin-left:-8px;margin-right:10px;min-width:28px;width:28px}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{height:16px;margin-left:-6px;margin-right:8px;min-width:16px;width:16px}.nsm7Bb-HzV7m-LgbsSe.Bz112c-LgbsSe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-radius:3px;margin-left:2px;margin-right:0;padding:0}.nsm7Bb-HzV7m-LgbsSe.JGcpL-RbRzK .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-radius:18px}.nsm7Bb-HzV7m-LgbsSe.pSzOP-SxQuSe.JGcpL-RbRzK .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-radius:14px}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe.JGcpL-RbRzK .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-radius:8px}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-bN97Pc-sM5MNb{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-align-items:center;-webkit-box-align:center;align-items:center;-webkit-flex-direction:row;-webkit-box-orient:horizontal;-webkit-box-direction:normal;flex-direction:row;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-flex-wrap:nowrap;flex-wrap:nowrap;height:100%;position:relative;width:100%}.nsm7Bb-HzV7m-LgbsSe .oXtfBe-l4eHX{-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-BPrWId{-webkit-flex-grow:1;-webkit-box-flex:1;flex-grow:1;font-family:Google Sans,arial,sans-serif;font-weight:500;overflow:hidden;text-overflow:ellipsis;vertical-align:top}.nsm7Bb-HzV7m-LgbsSe.purZT-SxQuSe .nsm7Bb-HzV7m-LgbsSe-BPrWId{font-weight:300}.nsm7Bb-HzV7m-LgbsSe .oXtfBe-l4eHX .nsm7Bb-HzV7m-LgbsSe-BPrWId{-webkit-flex-grow:0;-webkit-box-flex:0;flex-grow:0}.nsm7Bb-HzV7m-LgbsSe .nsm7Bb-HzV7m-LgbsSe-MJoBVe{-webkit-transition:background-color .218s;transition:background-color .218s;bottom:0;left:0;position:absolute;right:0;top:0}.nsm7Bb-HzV7m-LgbsSe:focus,.nsm7Bb-HzV7m-LgbsSe:hover{box-shadow:none;border-color:rgb(210,227,252);outline:none}.nsm7Bb-HzV7m-LgbsSe:focus-within{outline:2px solid #00639b;border-color:transparent}.nsm7Bb-HzV7m-LgbsSe:hover .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(66,133,244,.08)}.nsm7Bb-HzV7m-LgbsSe:active .nsm7Bb-HzV7m-LgbsSe-MJoBVe,.nsm7Bb-HzV7m-LgbsSe:focus .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(66,133,244,.1)}.nsm7Bb-HzV7m-LgbsSe.i5vt6e-to915-Ia7Qfc:hover .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(255,255,255,.08)}.nsm7Bb-HzV7m-LgbsSe.i5vt6e-to915-Ia7Qfc:active .nsm7Bb-HzV7m-LgbsSe-MJoBVe,.nsm7Bb-HzV7m-LgbsSe.i5vt6e-to915-Ia7Qfc:focus .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(255,255,255,.12)}.nsm7Bb-HzV7m-LgbsSe.MFS4be-Ia7Qfc:hover .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(255,255,255,.24)}.nsm7Bb-HzV7m-LgbsSe.MFS4be-Ia7Qfc:active .nsm7Bb-HzV7m-LgbsSe-MJoBVe,.nsm7Bb-HzV7m-LgbsSe.MFS4be-Ia7Qfc:focus .nsm7Bb-HzV7m-LgbsSe-MJoBVe{background:rgba(255,255,255,.32)}.nsm7Bb-HzV7m-LgbsSe .n1UuX-DkfjY{border-radius:50%;display:-webkit-box;display:-webkit-flex;display:flex;height:20px;margin-left:-4px;margin-right:8px;min-width:20px;width:20px}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId{font-family:Roboto;font-size:12px;text-align:left}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff .fmcmS,.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId .ssJRIf{overflow:hidden;text-overflow:ellipsis}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-align-items:center;-webkit-box-align:center;align-items:center;color:#5f6368;fill:#5f6368;font-size:11px;font-weight:400}.nsm7Bb-HzV7m-LgbsSe.jVeSEe.i5vt6e-to915-Ia7Qfc .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff{color:#c4c7c5;fill:#c4c7c5}.nsm7Bb-HzV7m-LgbsSe.jVeSEe.MFS4be-Ia7Qfc .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff{color:#e8eaed;fill:#e8eaed}@media screen and (prefers-contrast:more){.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff,.nsm7Bb-HzV7m-LgbsSe.jVeSEe.MFS4be-Ia7Qfc .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff{color:#000;fill:#000}}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-BPrWId .K4efff .Bz112c{height:18px;margin:-3px -3px -3px 2px;min-width:18px;width:18px}.nsm7Bb-HzV7m-LgbsSe.jVeSEe .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-top-left-radius:0;border-bottom-left-radius:0;border-top-right-radius:3px;border-bottom-right-radius:3px;margin-left:12px;margin-right:-10px}.nsm7Bb-HzV7m-LgbsSe.jVeSEe.JGcpL-RbRzK .nsm7Bb-HzV7m-LgbsSe-Bz112c-haAclf{border-radius:18px}.L5Fo6c-sM5MNb{border:0;display:block;left:0;position:relative;top:0}.L5Fo6c-bF1uUb{border-radius:4px;bottom:0;cursor:pointer;left:0;position:absolute;right:0;top:0}.L5Fo6c-bF1uUb:focus{border:none;outline:none}sentinel{}</style><link id="googleidentityservice" type="text/css" media="all" href="https://accounts.google.com/gsi/style" rel="stylesheet"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4170-2e4ec80e1a0c.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/6843-fa506b71dc5d.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/5837-827d4d74e66a.css"><link rel="home" href="https://www.agoda.com/" data-react-helmet="true"><meta name="robots" content="noindex, nofollow" data-react-helmet="true"><meta name="description" content="Find hotels in Cox&amp;#39;s Bazar, Bangladesh and explore top accommodation in the city. Check out star rating and review score before you book!" data-react-helmet="true"><meta name="keywords" content="11 Best Hotels in Cox&amp;#39;s Bazar, Bangladesh" data-react-helmet="true"><meta property="og:site_name" content="Agoda.com" data-react-helmet="true"><meta property="og:locale" content="en-us" data-react-helmet="true"><meta property="og:url" content="https://www.agoda.com/search" data-react-helmet="true"><meta property="og:type" content="place" data-react-helmet="true"><meta property="og:street_address" content="" data-react-helmet="true"><meta property="og:title" content="Agoda | Hotels in Cox&amp;#39;s Bazar | Best Price Guarantee!" data-react-helmet="true"><meta property="og:description" content="Find hotels in Cox&amp;#39;s Bazar, Bangladesh and explore top accommodation in the city. Check out star rating and review score before you book!" data-react-helmet="true"><meta property="og:image" content="https://cdn6.agoda.net/images/mvc/default/agoda-logo-flat-2019.png" data-react-helmet="true"><meta property="og:image:alt" content="" data-react-helmet="true"><meta property="fb:app_id" content="161134343906646" data-react-helmet="true"><meta name="twitter:card" content="summary" data-react-helmet="true"><meta name="twitter:site" content="@agoda" data-react-helmet="true"><meta name="twitter:creator" content="@agoda" data-react-helmet="true"><meta name="twitter:title" content="Agoda | Hotels in Cox&amp;#39;s Bazar | Best Price Guarantee!" data-react-helmet="true"><meta name="twitter:description" content="Find hotels in Cox&amp;#39;s Bazar, Bangladesh and explore top accommodation in the city. Check out star rating and review score before you book!" data-react-helmet="true"><meta name="twitter:url" content="https://www.agoda.com/search" data-react-helmet="true"><meta name="twitter:image" content="https://cdn6.agoda.net/images/mvc/default/agoda-logo-flat-2019.png" data-react-helmet="true"><meta name="apple-ios-itunes-app" content="app-id=440676901" data-react-helmet="true"><meta name="google-play-app" content="com.agoda.mobile.consumer" data-react-helmet="true"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/1860-9a2263b2a60a.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/2481-1393f55b7097.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/8564-5fc754378659.css"><link rel="stylesheet" type="text/css" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4490-830fbdcdd63c.css">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ url('/android-chrome-512x512.png') }}">
    <meta property="og:site_name" content="Prime Booking">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/agoda/agoda-search-master-bundle.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/agoda/agoda-search-exact.css') }}?v={{ time() }}">
    <style>
        .fuICnK, [class*="ScreenReaderOnly"] { position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important; }
        .ftJhvu { flex: 0 0 33.3333% !important; max-width: 33.3333% !important; width: 33.3333% !important; }
        .eKqvXe { flex: 0 0 41.6667% !important; max-width: 41.6667% !important; width: 41.6667% !important; }
        .fTvBdC { flex: 0 0 25.0000% !important; max-width: 25.0000% !important; width: 25.0000% !important; }
        .full-funnel-banner { display: flex !important; align-items: center !important; }
        .full-funnel-background { display: block !important; width: 100% !important; background: linear-gradient(88deg, #ffe8d6, #fed5d5) !important; }

        /* Agoda Mobile 1:1 Responsive Overrides */
        @media (max-width: 991.98px) {
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
            }
            .container-agoda {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 10px !important;
            }
            .SearchBoxContainer, .Searchbox__wrapper {
                width: 100% !important;
                max-width: 100% !important;
            }
            .Searchbox--horizontal .abb64-flex-row {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .Searchbox--horizontal .IconBox,
            .Searchbox--horizontal .IconBox--autocomplete,
            .Searchbox--horizontal .IconBox--occupancy,
            .SearchBoxContainer--compact .IconBox--checkIn,
            .SearchBoxContainer--compact .IconBox--checkOut,
            .Searchbox .IconBox {
                width: 100% !important;
                max-width: 100% !important;
            }
            .Searchbox .abb64-inline-flex {
                width: 100% !important;
            }
            .TopBarContainer, #topbar {
                position: static !important;
            }
            #searchPageLeftColumn {
                display: none !important;
            }
            .l-col-9, .l-col-8, .main-column > div:not(#searchPageLeftColumn),
            div[class*="Headerstyled__RightContainer"] {
                width: 100% !important;
                max-width: 100% !important;
                float: none !important;
            }
            .ssr-container, .l-row, .main-column {
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }
            /* Stack Property Cards Vertically on Mobile */
            .ftJhvu, .eKqvXe, .fTvBdC {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .fTvBdC {
                border-top: 1px solid #f1f5f9 !important;
                padding-top: 10px !important;
            }
            .ssr-search-result-item {
                padding: 8px 0 !important;
            }
            header#page-header {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .Header__LogoAndLinks__Links {
                display: none !important;
            }
            #fullFunnelBannerTarget, .full-funnel-background, .full-funnel-banner-with-close {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }
            .full-funnel-header-sub {
                word-break: break-word !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer">
    <style>
        /* Sort dropdown */
        .pb-sort-select { width: 100%; min-height: 40px; border: 0; border-radius: 8px; padding: 0; font-size: 16px; color: #2a2a2e; background: #fff; cursor: pointer; font-family: inherit; }
        .pb-sort-select:focus { outline: none; }

        /* Pagination */
        .pagination-panel { clear: both; padding: 16px 0 8px; }
        #paginationContainer { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 12px; }
        .pb-page-btn { display: inline-flex; align-items: center; height: 40px; padding: 0 18px; border: 1px solid #2067e1; border-radius: 8px; color: #2067e1; background: #fff; font-weight: 600; font-size: 14px; text-decoration: none; }
        .pb-page-btn:hover { background: #2067e1; color: #fff; }

        /* Breadcrumb: full-width row below both columns, never under the sticky filter column */
        .main-column { display: flow-root; }
        #breadcrumb { clear: both; background: #f8f9fa; margin: 24px 0 0 16px !important; padding: 12px 16px; border-top: 1px solid #e5e7eb; border-radius: 6px; }
        .pb-breadcrumb ol { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: #4a4f57; }
        .pb-breadcrumb li + li::before { content: "\203A"; margin-right: 6px; color: #8a9099; }
        .pb-breadcrumb a { color: #2067e1; text-decoration: none; }
        .pb-breadcrumb a:hover { text-decoration: underline; }
        .pb-crumb-count { color: #8a9099; }
        #searchPageLeftColumn { max-height: calc(100vh - 20px); }

        /* Footer: this page does not load Bootstrap, so its grid helpers are defined here. */
        body > footer, footer { clear: both; display: block; width: 100%; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        footer *, footer *::before, footer *::after { box-sizing: border-box; }
        footer .container { width: 100%; max-width: 1320px; margin: 0 auto; padding-left: 16px; padding-right: 16px; }
        footer .row { display: flex; flex-wrap: wrap; margin-left: -12px; margin-right: -12px; }
        footer .row > * { padding-left: 12px; padding-right: 12px; margin-top: 24px; flex: 0 0 auto; width: 100%; }
        footer .col-6 { width: 50%; }
        @media (min-width: 768px) { footer .col-md-4 { width: 33.3333%; } footer .col-md-6 { width: 50%; } }
        @media (min-width: 992px) { footer .col-lg-2 { width: 16.6667%; } footer .col-lg-3 { width: 25%; } footer .col-lg-4 { width: 33.3333%; } footer .col-lg-6 { width: 50%; } }
        footer .list-unstyled { list-style: none; padding-left: 0; margin: 0; }
        footer h6 { margin-top: 0; }
        footer a { text-decoration: none; }
        footer .d-flex { display: flex !important; }
        footer .flex-wrap { flex-wrap: wrap !important; }
        footer .align-items-center { align-items: center !important; }
        footer .justify-content-between { justify-content: space-between !important; }
        footer .justify-content-center { justify-content: center !important; }
        footer .text-center { text-align: center !important; }
        footer .gap-2 { gap: .5rem !important; } footer .gap-3 { gap: 1rem !important; }
        footer .mb-0 { margin-bottom: 0 !important; } footer .mb-2 { margin-bottom: .5rem !important; } footer .mb-3 { margin-bottom: 1rem !important; } footer .mb-4 { margin-bottom: 1.5rem !important; }
        footer .mt-2 { margin-top: .5rem !important; } footer .mt-3 { margin-top: 1rem !important; } footer .me-1 { margin-right: .25rem !important; } footer .me-2 { margin-right: .5rem !important; }
        footer .pb-2 { padding-bottom: .5rem !important; } footer .py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        footer .border-bottom { border-bottom: 1px solid #dee2e6 !important; } footer .border-top { border-top: 1px solid #dee2e6 !important; }
        footer .border-light { border-color: #f8f9fa !important; }
        footer .text-danger { color: #dc3545 !important; } footer .text-primary { color: #0d6efd !important; } footer .text-info { color: #0dcaf0 !important; }
        footer .text-muted { color: #6c757d !important; } footer .small { font-size: .875em; } footer .fw-semibold { font-weight: 600 !important; } footer .fw-bold { font-weight: 700 !important; }
        footer .text-decoration-none { text-decoration: none !important; }
        footer img, footer svg { vertical-align: middle; }
    </style>
</head>

<body class="NewSSR desktop AVENGERS-2892 AVENGERS-2888 AVENGERS-3602 AVENGERS-3989 TRIPWL-1417 ltr themed theme-agoda ads-agoda ads-agoda-deprecateInaccessibleColor ads-agoda-isInteractiveStateImproved window-loaded" style="margin-right: 0px; position: relative;">
    <div id="mockDataEnabled" value="False"></div>
    <div id="login-react-popup"></div>
    <div id="login-react-root"></div>
    <div id="pointsmax-panel-react-root" data-hotel-id="" data-signin-landing=""></div>

    <input id="backwardRequestVerificationToken" name="backwardRequestVerificationToken" type="hidden" value="oYKqRyKVa/TxUbbQx9dGY0eztKS3Pki0FUT1bt+4b0Q=:YKoNvrYQYi4pi2BdfVFDnWdvqsk=">

<input id="requestVerificationToken" name="requestVerificationToken" type="hidden" value="CfDJ8Dkuqwv-0VhLoFfD8dw7lYyhgLAIbTiSMvhGp8wQHPVKPyJ4HTCNSNzS7ugNAED9zz3yiTpBwmgJiuWIA_BgPk37p8tAAo-LiEzigxtXowVhEFLzxJukaabCDUjQSORdGYHMQfbmZSd7EniU3VCk5wk">


    <style>
        .backgroundSpace {
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .backgroundContainer {
            width: 90vw;
            height: 90vh;
        }
    </style>
    <div class="backgroundSpace" data-lcp="false">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <div class="backgroundContainer"></div>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>

















    


 

        <div id="consent-banner-container"></div>



<div id="pointsmax-panel-react-root" data-hotel-id="" data-signin-landing=""></div>



<div class="hide" id="page-identification" data-page="NewSSR" data-device="desktop"></div>
<div id="seo-data"></div>






    





<style>
    .no-scroll {
        overflow-y: hidden;
    }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>

    <style>
        /* Keep the hydrated package summary behind the overlay for its entire fade-out. */
        #ssrPlaceholder ~ #searchPageReactRoot #reactSearchPage.package #topbar {
            visibility: hidden;
        }

        /* Release only this summary if the placeholder's transition end event is missed. */
        #ssrPlaceholder.ssr-placeholder--hidden ~ #searchPageReactRoot #reactSearchPage.package #topbar {
            animation: reveal-packages-search-summary 1ms linear 400ms forwards;
        }

        @keyframes reveal-packages-search-summary {
            to {
                visibility: visible;
            }
        }
    </style>



<div id="css-dummy"></div>



<header id="page-header"><section data-element-name="header-container-section" data-selenium="header-container-section" class="Headerstyled__HeaderSection-sc-vtperc-0 fyCga-D"><div data-element-name="header-left-section" data-selenium="header-left-section" class="Box-sc-kv6pi1-0 kGmOyP"><nav class="Header__LogoAndLinks"><div class="Box-sc-kv6pi1-0 PrimaryLogo__PrimaryLogoWrapper-sc-111tzin-0 eirVlt lcqCvH Header__LogoAndLinks__PrimaryLogo"><a href="{{ url('/') }}" class="Header__LogoAndLinks__PrimaryLogo__LogoLink" aria-label="Prime Booking logo Home link" data-element-name="primary-logo"><div class="Box-sc-kv6pi1-0 jsTHYe" style="display: flex; align-items: center;"><img src="{{ asset('images/logo-brand.png') }}" style="height: 32px; width: auto; object-fit: contain;" alt="Prime Booking"></div></a></div><div class="Box-sc-kv6pi1-0 LinkContainerstyled__LinkContainerStyled-sc-1iblxzq-8 lhYHNm kHnLKl Header__LogoAndLinks__Links" linkscount="8" data-element-name="header-link-container" data-selenium="header-link-container"><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-hotels"><a href="/" class="" data-element-name="header-hotels-links" data-selenium="header-hotels"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Hotels &amp; Homes</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-transportation"><div class="MenuLinkstyled__BadgeContainer-sc-rfzdfl-1 iaLHVP"><div class="Box-sc-kv6pi1-0 iCpMWZ Badgestyled__BadgeStyled-sc-110jpdn-0 gIbXsK MenuLink__MenuBadgeStyled-sc-8it71c-0 kBwZcI"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 cutlhX kite-js-Typography ">New!</span></div></div><div class="LinkContainerstyled__DroneEllipsisLinkStyled-sc-1iblxzq-4 bUFuFX"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex      "><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-1      "><a class="" data-element-name="header-transportation-links" data-selenium="header-transportation" role="button" aria-expanded="false" tabindex="0"><div class="abb64-flex abb64-flex-row"><span class="MenuLinkstyled__DropdownContainer-sc-rfzdfl-2 cZiOcy"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Transport</span></span><div class="abb64-mx-8 abb64-justify-center abb64-items-center abb64-px-0 abb64-py-0 abb64-flex abb64-flex-row"><span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline"><svg viewBox="0 0 1 1" role="presentation" class="" style="width: 12px; height: 12px; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/ebd4b0d9.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%"></rect></svg></span></div></div></a></div></div></div></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-activities"><a href="https://www.getyourguide.com/?partner_id=HYFZ1GI&amp;cmp=desktop" target="_blank" class="" data-element-name="header-activities-links" data-selenium="header-activities"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Things to do</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-deals"><a href="/deals" target="_blank" class="" data-element-name="header-deals-links" data-selenium="header-deals"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Coupons &amp; Deals</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-apartments"><a href="/apartments" target="_blank" class="" data-element-name="header-apartments-links" data-selenium="header-apartments"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Apartments</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-esim"><a href="/clickouts/esim?source=desktop&amp;medium=-1&amp;campaign=header_esim_link&amp;currency=USD" target="_blank" class="" data-element-name="header-esim-links" data-selenium="header-esim"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">eSIM</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-travel-guides"><a href="/travel-guides" target="_blank" class="" data-element-name="header-travel-guides-links" data-selenium="header-travel-guides"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Travel Guides</span></a></div></div><div class="LinkContainerstyled__LinkStyled-sc-1iblxzq-1 fgYoAL"><div class="LinkContainerstyled__MenuItemContainer-sc-1iblxzq-0 fkNAwX Header__LogoAndLinks__Links__Links" data-selenium="header-travel-tips"><a href="/traveltips/" target="_blank" class="" data-element-name="header-travel-tips-links" data-selenium="header-travel-tips"><span class="Typographystyled__TypographyStyled-sc-j18mtu-0 aXOsA kite-js-Typography __text">Travel Itineraries</span></a></div></div><div class="LinkContainerstyled__DroneEllipsisLinkStyled-sc-1iblxzq-4 bUFuFX"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex      "><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-1      "><div class="abb64-box LinkContainerstyled__DroneEllipsisIconContainer-sc-1iblxzq-7 fbuKcD abb64-fill-inherit abb64-text-inherit      "><button aria-label="Travel menu" aria-expanded="false" type="button" class="abb64-box abb64-bg-generic-base-transparent abb64-fill-inherit abb64-text-inherit abb64-cursor-pointer abb64-pl-24   abb64-border-0   "><span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline"><svg viewBox="0 0 1 1" role="presentation" class="" style="width: 20px; height: 20px; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/6714c51c.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%"></rect></svg></span></button></div></div></div></div></div></nav></div><div data-element-name="header-right-section" data-selenium="header-right-section" class="Box-sc-kv6pi1-0 Headerstyled__RightContainer-sc-vtperc-1 QXKtQ gsqccF" style="min-width: min-content; flex: 1 1 0%;"><div data-testid="responsive-action-bar" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-min-w-min abb64-items-center abb64-flex abb64-justify-end abb64-overflow-visible abb64-px-8      " style="height: 60px;"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-flex-none      " style="height: 60px; display: none;"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-min-w-fit abb64-self-center abb64-whitespace-nowrap      "><button data-element-name="header-list-your-place-button" data-selenium="header-list-your-place-button" type="button" class="abb64-box abb64-bg-generic-base-surface abb64-border-generic-base-default abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-border-solid abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-px-24 abb64-py-12  abb64-rounded-base abb64-border-1  abb64-interactive  " style="height: 44px; --interactive-color: 32, 103, 218; --interactive-color-primary: var(--ads-colors-primary-base-intent); --color-neutral-4: var(--ads-colors-generic-base-inverted);"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row      "><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bkxKct">List your place</span></div></button></div></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-none      " style="height: 60px; display: flex;"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-flex-none      " style="height: 60px;"><div data-testid="header-box" role="button" tabindex="0" aria-label="Language in English" aria-describedby="language-container-selected-language-desc" data-element-name="language-container-selected-language" data-selenium="language-container-selected-language" data-value="en-us" class="Box-sc-kv6pi1-0 jcGjhQ" data-mimir-element-data="agoda-layout"><div class="HeaderItem__HeaderItemContainer-sc-1xwqe53-0 bxrBfG"><img class="Flag__StyledCdnIcon-sc-nifs7d-0 jaaQex" src="https://cdn6.agoda.net/images/mobile/flag-us@2x.png" alt="English" width="26" height="auto"></div><span id="language-container-selected-language-desc" class="ScreenReaderOnly__ScreenReaderOnlyStyled-sc-szxtre-0 fuICnK">Select your language</span></div></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-flex-none      " style="height: 60px;"><div data-testid="header-box" role="button" tabindex="0" aria-label="Price display in US Dollar" aria-describedby="currency-container-selected-currency-desc" data-element-name="currency-container-selected-currency" data-selenium="currency-container-selected-currency" data-value="USD" class="Box-sc-kv6pi1-0 jcGjhQ" data-mimir-element-data="agoda-layout"><div class="HeaderItem__HeaderItemContainer-sc-1xwqe53-0 bxrBfG"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row      "><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN dOLNKL">BDT</span></div></div><span id="currency-container-selected-currency-desc" class="ScreenReaderOnly__ScreenReaderOnlyStyled-sc-szxtre-0 fuICnK">Select your currency</span></div></div></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-none      " style="height: 60px; display: flex;"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-none" style="height: 60px;">
@auth
    <div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-items-center" style="margin-left: 12px;">
        <a href="{{ route('user.dashboard') }}" style="color: #2a2a2e; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 6px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>{{ Auth::user()->name }}</span>
        </a>
    </div>
@else
    <a href="{{ route('login') }}" class="abb64-box abb64-bg-generic-base-transparent abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-mr-8 abb64-px-24 abb64-py-12 abb64-rounded-base abb64-border-0 abb64-interactive" style="height: 44px; text-decoration: none; display: flex; align-items: center;">
        <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bkxKct" style="color: #2067e1; font-weight: 600;">Sign in</span>
    </a>
    <a href="{{ route('register') }}" class="abb64-box abb64-bg-generic-base-surface abb64-border-generic-base-default abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-border-solid abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-px-24 abb64-py-12 abb64-rounded-base abb64-border-1 abb64-interactive" style="height: 44px; text-decoration: none; display: flex; align-items: center; border: 1.5px solid #d1d5db;">
        <span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bkxKct" style="color: #2067e1; font-weight: 600;">Create account</span>
    </a>
@endauth
</div></div></div><div data-testid="fixed-marker" class="abb64-box abb64-fill-inherit abb64-text-inherit      " style="width: 1px;"></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-content-end abb64-self-center abb64-flex abb64-mx-8 abb64-my-0      "></div><div class="Box-sc-kv6pi1-0 flviCy"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-flex-1      "><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex abb64-flex-1      "><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-1      "><button aria-label="Account Menu" data-element-name="header-hamburger-menu" data-selenium="header-hamburger-menu" aria-haspopup="listbox" aria-expanded="false" tabindex="0" type="button" class="abb64-box abb64-bg-generic-base-transparent abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-px-12 abb64-py-12  abb64-rounded-max abb64-border-0  abb64-interactive  " style="width: 44px; height: 44px; --interactive-color: 32, 103, 218; --interactive-color-primary: var(--ads-colors-primary-base-intent); --color-neutral-4: var(--ads-colors-generic-base-inverted);"><span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline"><svg viewBox="0 0 1 1" role="presentation" class="" style="width: 20px; height: 20px; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/3094a487.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%"></rect></svg></span></button></div></div></div></div></div></section></header>

<div id="searchPageReactRoot"><div id="reactSearchPage" class="SearchPage" style=""><div class="TopBarContainer"><div id="topbar" class="topbar-wrapper show-search" data-testid="topbar"><form action="{{ route('search.index') }}" method="GET" id="searchHeaderForm">
    <input type="hidden" name="search_type" value="{{ $searchType ?? 'hotel' }}">
    <input type="hidden" name="check_in" id="checkInHiddenInput" value="{{ $checkinCarbon->format('Y-m-d') }}">
    <input type="hidden" name="check_out" id="checkOutHiddenInput" value="{{ $checkoutCarbon->format('Y-m-d') }}">
    <input type="hidden" name="guests" id="guestsHiddenInput" value="{{ $guestCount }}">
    <input type="hidden" name="rooms" id="roomsHiddenInput" value="{{ $roomsCount }}">
    <input type="date" id="checkInNativePicker" style="position:absolute; opacity:0; pointer-events:none;" value="{{ $checkinCarbon->format('Y-m-d') }}" onchange="document.getElementById('checkInHiddenInput').value=this.value; updateSearchDateDisplay('checkIn', this.value);">
    <input type="date" id="checkOutNativePicker" style="position:absolute; opacity:0; pointer-events:none;" value="{{ $checkoutCarbon->format('Y-m-d') }}" onchange="document.getElementById('checkOutHiddenInput').value=this.value; updateSearchDateDisplay('checkOut', this.value);">
<div class="SearchBoxContainer SearchBoxContainer--AllRooms SearchBoxContainer--compact SearchBoxContainer--noanimation SearchBoxContainer--ImproveTextSearch" id="SearchBoxContainer" data-selenium="normal-property-searchbox"><div class="Searchbox Searchbox--horizontal" data-selenium="searchBox"><div class="Searchbox__wrapper"><div class="abb64-flex-1 abb64-content-center abb64-flex abb64-flex-row"><div tabindex="-1" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--autocomplete" data-selenium="autocomplete-box" data-element-name="autocomplete-box" data-element-value="prefilled" id="autocomplete-box"><div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper"><span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-search-box" data-selenium="ficon-icon-box"></i></span><div class="IconBox__child" data-selenium="icon-box-child"><input aria-label="Enter a destination or property" role="combobox" aria-expanded="false" aria-controls="search-box-autocomplete-id" aria-describedby="instruction-text" data-selenium="textInput" tabindex="0" type="text" class="SearchBoxTextEditor SearchBoxTextEditor--autocomplete" placeholder="Enter a destination or property" id="textInput" aria-autocomplete="list" aria-haspopup="listbox" value="{{ $destLabel }}" name="destination" id="mainDestInput"><span id="instruction-text" class="ScreenReaderOnly__ScreenReaderOnlyStyled-sc-szxtre-0 fuICnK">Begin typing property name or keyword to search, use arrow keys or tab key to navigate, press Enter to select</span></div></div></div><div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--checkIn IconBox--focussable" data-element-name="check-in-box" role="button" aria-expanded="false" aria-label="Check-in 11 Nov 2026 Wednesday" id="check-in-box" onclick="document.getElementById('checkInNativePicker').showPicker();" style="cursor: pointer;"><div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper"><span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-check-in" data-selenium="ficon-icon-box"></i></span><div class="IconBox__child" data-selenium="icon-box-child"><div data-selenium="checkInBox" data-date="2026-11-11" class="SearchBoxTextDescription SearchBoxTextDescription--checkIn SearchBoxTextDescription--focussable"><div class="SearchBoxTextDescription__title" data-selenium="checkInText" id="checkInDisplayDate">{{ $checkinCarbon->format('j M Y') }}</div><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" id="checkInDisplayDay">{{ $checkinCarbon->format('l') }}</span></div></div></div></div><div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--checkOut IconBox--focussable" data-element-name="check-out-box" role="button" aria-expanded="false" aria-label="Check-out 13 Nov 2026 Friday" id="check-out-box" onclick="document.getElementById('checkOutNativePicker').showPicker();" style="cursor: pointer;"><div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper"><span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-check-out" data-selenium="ficon-icon-box"></i></span><div class="IconBox__child" data-selenium="icon-box-child"><div data-selenium="checkOutBox" data-date="2026-11-13" class="SearchBoxTextDescription SearchBoxTextDescription--checkOut SearchBoxTextDescription--focussable"><div class="SearchBoxTextDescription__title" data-selenium="checkOutText" id="checkOutDisplayDate">{{ $checkoutCarbon->format('j M Y') }}</div><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" id="checkOutDisplayDay">{{ $checkoutCarbon->format('l') }}</span></div></div></div></div><div tabindex="0" class="Box-sc-kv6pi1-0 sc-fUnMCh bxexcZ jDzUYD IconBox IconBox--occupancy IconBox--focussable" data-element-name="occupancy-box" role="button" aria-expanded="false" aria-label="Guests and rooms 2&nbsp;adults 1&nbsp;room" id="occupancy-box" onclick="toggleOccupancyDropdown(event)" style="cursor: pointer; position: relative;"><div class="Box-sc-kv6pi1-0 krcUda IconBox__wrapper"><span tabindex="-1"><i class="ficon IconBox__icon ficon-20 ficon-travelers" data-selenium="ficon-icon-box"></i></span><div class="IconBox__child" data-selenium="icon-box-child"><div data-selenium="occupancyBox" data-value="1" class="SearchBoxTextDescription SearchBoxTextDescription--occupancy"><div class="SearchBoxTextDescription__title"><span data-selenium="adultValue" class="abb64-box abb64-fill-inherit abb64-text-inherit" id="adultsDisplayVal">{{ $guestCount }}&nbsp;adult{{ $guestCount > 1 ? 's' : '' }}</span></div><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" data-selenium="roomValue" class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bDkwZu" id="roomsDisplayVal">{{ $roomsCount }}&nbsp;room{{ $roomsCount > 1 ? 's' : '' }}</span><div class="SearchBoxTextDescription__child"><i class="ficon ficon ficon-12 ficon-thin-arrow-down"></i></div></div></div></div></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-inline-flex      " style="width: 162px;"><button data-selenium="searchButton" data-element-name="search-button" data-active-pressed="true" type="submit" class="abb64-box abb64-bg-product-primary abb64-fill-generic-base-inverted abb64-text-generic-base-inverted abb64-w-full abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-mt-2 abb64-px-24 abb64-py-12  abb64-rounded-base abb64-border-0   abb64-interactive-style abb64-interactive-primary abb64-focus-ring-interactive-border abb64-interactive-bg  " style="min-height: 44px;"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row      "><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN jXHxke">SEARCH</span></div></button></div></div></div></div><noscript></noscript> </div></form></div><div class="TopBarContainer__Buffer TopBarContainer__Buffer--moveFilterToLeftSidebar" data-testid="bufferBetweenTopBarAndFullFunnelBannerTarget"></div><div id="fullFunnelBannerTarget" data-testid="fullFunnelBannerTarget"><div id="full-funnel-banner-container"><div class="full-funnel-background full-funnel-static full-funnel-orange-pink accessible-contrast" data-selenium="full-funnel-banner"><div class="full-funnel-banner-with-close"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-float-right abb64-mt-8      "><button aria-label="Close" data-element-name="full-funnel-banner-close-drone" type="button" class="abb64-box abb64-bg-generic-base-transparent abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-px-12 abb64-py-12  abb64-rounded-max abb64-border-0  abb64-interactive  " style="width: 44px; height: 44px; --interactive-color: 32, 103, 218; --interactive-color-primary: var(--ads-colors-primary-base-intent); --color-neutral-4: var(--ads-colors-generic-base-inverted);"><svg role="img" aria-hidden="true" opacity="1" class="sc-dcJsrY Iconstyled__IconStyled-sc-1fugtov-0  hMRXsE"><use href="#9523eea2"></use></svg></button></div><div class="full-funnel-banner" data-selenium="full-funnel-banner-inner" data-element-name="full-funnel-banner"><img alt="null image" class="full-funnel-image" src="https://cdn0.agoda.net/images/emailmarketing/Web_SSR_banner/webbanner_coupon.png"><div class="full-funnel-header no-theme"><h2 class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN dgzjZX" data-testid="full-funnel-banner-header-title">Looking for instant coupons?</h2><div class="full-funnel-header-sub"><span class="full-funnel-header-sub-text">	
Check out our Coupons &amp; Deals page for today's discounts</span></div></div><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-absolute      " style="transform: translateY(-50%); top: 50%; right: 0px;"><a target="_blank" href="{{ route('deals') }}" class="abb64-box abb64-bg-generic-base-surface abb64-border-generic-base-default abb64-fill-product-primary abb64-text-product-primary abb64-items-center abb64-border-solid abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-no-underline abb64-px-24 abb64-py-12  abb64-rounded-base abb64-border-1  abb64-interactive  " style="height: 44px; --interactive-color: 32, 103, 218; --interactive-color-primary: var(--ads-colors-primary-base-intent); --color-neutral-4: var(--ads-colors-generic-base-inverted);"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-items-center abb64-flex abb64-flex-row      "><span class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN bkxKct">	
See all coupons</span></div></a></div></div></div></div></div></div></div><div class="container-agoda SearchPage--smallscreen"><div class="ssr-container"><div class="l-row"><div class="main-column"><div id="searchPageLeftColumn" class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-w-1/4 abb64-h-screen abb64-flex abb64-flex-col abb64-float-left abb64-overflow-scroll abb64-sticky abb64-pb-32 abb64-pl-16 abb64-gap-16      " style="top: 20px;">
@include('components.search.filter-sidebar')
</div><div id="searchPageRightColumn" class="l-col-9"><div id="contentContainer"><div data-policy-eligible-actions="search-ubt-availability-message|messaging-banner|family-mode-banner" data-correlation-id="" role="region" aria-label="Announcement" data-element-name="hero-banner-container" data-element-value="messaging-banner" class="abb64-box abb64-fill-inherit abb64-text-inherit      " style=""><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-mb-12      "><div data-element-name="messaging-banner" data-element-index="0" data-element-value="claim-discount" class="Box-sc-kv6pi1-0 krcUda Bannerstyled__BannerContainer-sc-eh76yq-0 kYiFjp"><div class="Box-sc-kv6pi1-0 eIkUxy"><div class="abb64-box abb64-fill-inherit abb64-text-inherit abb64-flex      "><svg width="1em" height="1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="SvgIconstyled__SvgIconStyled-sc-1i6f60b-0 fFlpzG"><path fill-rule="evenodd" d="M16 5v2h1V5h5a1 1 0 0 1 1 1v3a3 3 0 0 0 0 6v3a1 1 0 0 1-1 1h-5v-2h-1v2H2a1 1 0 0 1-1-1v-3a3 3 0 0 0 0-6V6a1 1 0 0 1 1-1h14zm1 8h-1v2h1v-2zm0-4h-1v2h1V9z"></path></svg></div><div class="Box-sc-kv6pi1-0 fWXSeW"><div class="Box-sc-kv6pi1-0 jQvQJb"><h5 class="Typographystyled__TypographyStyled-sc-j18mtu-0 eskIcE kite-js-Typography ">Congratulations! You get an extra 5% OFF today.</h5></div><p class="Typographystyled__TypographyStyled-sc-j18mtu-0 jGiVLx kite-js-Typography "> Just activate your coupon now to unlock this offer.</p></div><div class="Bannerstyled__ButtonGroup-sc-eh76yq-1 dBpXGB"><button color="warning" class="Buttonstyled__ButtonStyled-sc-5gjk6l-0 kVcfFS"><div class="BaseButtonstyled__HiddenToggler-sc-12j2fzo-2 jpxjOF"><div class="Box-sc-kv6pi1-0 krcUda"><span class="Spanstyled__SpanStyled-sc-16tp9kb-0 kIaLZk kite-js-Span ">Activate coupon</span></div></div></button></div></div></div></div></div><div id="sort-bar"><div class="abb64-mb-8 abb64-mt-24 abb64-w-full abb64-justify-between abb64-items-center abb64-gap-16 abb64-flex abb64-flex-row" data-element-name="sort-bar-dropdown-container"><div class="abb64-shrink-0"><h1 class="sc-aXZVg Typographystyled__TypographyStyled-sc-1uoovui-0 ifcRDN dgzjZX" data-element-name="properties-available-text">{{ $destLabel }}: {{ number_format($totalCount) }} {{ $totalCount === 1 ? 'property' : 'properties' }}</h1></div><div class="abb64-shrink-0" style="width: 290px;"><div class="abb64-flex abb64-flex-col abb64-w-full abb64-gap-4 abb64-isolate"><div class="abb64-bg-generic-base-surface abb64-border-generic-state-inactive abb64-border-1 abb64-border-solid abb64-flex abb64-items-center abb64-rounded-8 abb64-w-full abb64-px-12 abb64-gap-4 abb64-relative abb64-interactive-style abb64-interactive-generic abb64-focus-ring-interactive-within abb64-focus-ring-interactive-border " data-active-pressed="true" data-has-label="false" style="min-height: 42px;"><form method="GET" action="{{ route('search.index') }}" id="sortForm" style="width: 100%; margin: 0;">
@foreach (request()->except(['sort_by', 'page']) as $k => $v)
@if (is_array($v))
@foreach ($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
@else<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
@endforeach
<select name="sort_by" id="sortBySelect" aria-label="Sort results" onchange="this.form.submit()" class="pb-sort-select">
@foreach ($sortOptions as $sortValue => $sortLabel)
<option value="{{ $sortValue }}" @selected($sortBy === $sortValue)>Sort by: {{ $sortLabel }}</option>
@endforeach
</select>
</form></div></div></div></div></div><ol class="hotel-list-container">
    @forelse($merged as $idx => $hotel)
        @include('components.search.property-card', ['item' => $hotel, 'isFirst' => ($idx === 0)])
    @empty
        <li class="abb64-p-32 abb64-text-center abb64-bg-generic-base-surface abb64-rounded-8 abb64-border-1" style="border: 1px solid #e2e8f0; list-style: none; margin: 20px 0; padding: 40px; text-align: center; background: #ffffff;">
            <h3 style="font-size: 20px; font-weight: 700; color: #2a2a2e; margin-bottom: 8px;">No properties found matching your criteria in {{ $destLabel }}</h3>
            <p style="color: #687176; font-size: 14px; margin-bottom: 20px;">Try adjusting your filters, budget, or dates to find available rooms.</p>
            <a href="{{ route('search.index', ['destination' => $destLabel]) }}" style="background: #2067e1; color: #fff; padding: 10px 24px; border-radius: 9999px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block;">Reset All Filters</a>
        </li>
    @endforelse
</ol><span role="status" aria-live="polite" data-testid="search-result-update" class="ScreenReaderOnly__ScreenReaderOnlyStyled-sc-szxtre-0 fuICnK">Search results updated. {{ number_format($totalCount) }} properties found.</span></div><div class="clearfix pagination-panel" data-selenium="pagination-panel" style=""><div><div id="paginationContainer" class="pagination-grid" data-selenium="pagination"><div class="abb64-justify-start abb64-flex abb64-flex-row">@if ($currentPage > 1)<a class="pb-page-btn" rel="prev" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">&lsaquo; Previous</a>@endif</div><div class="abb64-justify-center abb64-items-center abb64-flex abb64-flex-row"><span id="paginationPageCount" class="pagination2__text" data-selenium="pagination-text">Page {{ $currentPage }} of {{ $lastPage }}</span></div><div class="abb64-justify-end abb64-flex abb64-flex-row">@if ($currentPage < $lastPage)<a class="pb-page-btn" rel="next" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Next &rsaquo;</a>@endif</div></div></div></div></div><div class="popup-maps-root"></div></div><div class="row ssr-breadcrumb" id="breadcrumb"><div class="col-xs-12"><div class="clearfix"><nav aria-label="breadcrumbs" class="pb-breadcrumb">
<ol itemscope itemtype="https://schema.org/BreadcrumbList">
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('home') }}"><span itemprop="name">Home</span></a><meta itemprop="position" content="1"></li>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('search.index') }}"><span itemprop="name">Bangladesh</span></a><meta itemprop="position" content="2"></li>
@if (!empty($dest))
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page"><span itemprop="name">{{ $destLabel }}</span> <span class="pb-crumb-count">({{ number_format($totalCount) }})</span><meta itemprop="position" content="3"></li>
@endif
</ol>
</nav></div></div></div><div class="ad-column"><div class="AdsContainer AdsContainer--sizeS" data-testid="ads-banner-side-container"><div id="div-gpt-ad-1581655850769-0" data-element-name="ads-vertical-banner" class="AdsBanner sc-jsJBEP csZLff AdsSlot1" data-element-index="1" data-google-query-id="CIPs4p635JcDFV_chAAdouMiZg" style="display: none;"><div id="google_ads_iframe_/3102/Agoda.com_Hotels/Agoda_hotel_WEB_SSR_160x600_1_0__container__" style="border: 0pt; width: 160px; height: 0px;"></div></div><div id="div-gpt-ad-1581655880570-0" data-element-name="ads-vertical-banner" class="AdsBanner sc-jsJBEP csZLff AdsSlot2 sticky" data-element-index="2" data-google-query-id="CITs4p635JcDFV_chAAdouMiZg" style="display: none;"><div id="google_ads_iframe_/3102/Agoda.com_Hotels/Agoda_hotel_WEB_SSR_160x600_2_0__container__" style="border: 0pt; width: 160px; height: 600px;"></div></div></div></div></div></div><div id="sideColumnInfo" class="Box-sc-kv6pi1-0 krcUda"><div class="abb64-fixed abb64-flex abb64-flex-col" style="bottom: 62px; right: 29px; z-index: 100;"><div class="abb64-w-full abb64-flex abb64-flex-col" data-testid="save-to-trip-floating-content" data-element-name="save-to-trip-floating-content"><div class="abb64-self-end abb64-flex abb64-flex-col" data-testid="save-to-trip-floating-content-button" data-element-name="save-to-trip-floating-content-button"><button aria-label="My Trips" data-component="my-trips-floating-button" data-testid="my-trips-floating-button" data-element-name="my-trips-floating-action-button" data-element-id="my-trips-floating-action-button-01872b46-cce1-4bde-a177-e8959bf1049f" data-active-pressed="true" type="button" class="abb64-box abb64-bg-product-primary abb64-fill-generic-base-inverted abb64-shadow-bottom-m abb64-text-generic-base-inverted abb64-items-center abb64-cursor-pointer abb64-flex abb64-flex-col abb64-justify-center abb64-px-12 abb64-py-12  abb64-rounded-max abb64-border-0   abb64-interactive-style abb64-interactive-primary abb64-focus-ring-interactive-border abb64-interactive-bg  " style="width: 44px; height: 44px;" data-mimir-element-data="unified-saved"><span role="img" aria-hidden="true" class="abb64-inline-flex abb64-align-baseline"><svg viewBox="0 0 1 1" role="presentation" class="" style="width: 20px; height: 20px; mask: url(&quot;https://cdn6.agoda.net/cdn-design-system/icons/4a22653a.svg&quot;) left top / contain no-repeat;"><rect width="100%" height="100%"></rect></svg></span></button></div></div></div><div data-element-name="redirect-to-app" class="Box-sc-kv6pi1-0 fDkzXz redirectToApp"><div role="button" tabindex="0" data-element-name="redirect-to-app-qr" class="Box-sc-kv6pi1-0 hkJfID redirectToAppBar" aria-label="Download App" style="transition: width 500ms;"><svg width="1em" height="1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="SvgIconstyled__SvgIconStyled-sc-1i6f60b-0 gAmtww redirect-icon"><path fill-rule="evenodd" d="M17 1a2 2 0 0 1 2 2v18a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h10zm-3.5 19h-3a.5.5 0 0 0-.09.992l.09.008h3a.5.5 0 0 0 .09-.992L13.5 20zM17 4H7v14h10V4z"></path></svg><span class="Spanstyled__SpanStyled-sc-16tp9kb-0 cFzggl kite-js-Span text" style="position: fixed; visibility: hidden;">Download App</span></div></div></div></div></div></div>
<div id="page-backdrop" data-selenium="backdrop" class="hide"></div>




    
    @include('components.search.results-interactions', ['items' => $merged])
    <script>
    (function () {
        // Coupon banner: close button hides it, "Activate coupon" opens Coupons & Deals.
        document.addEventListener('click', function (e) {
            var close = e.target.closest('[data-element-name="full-funnel-banner-close-drone"]');
            if (close) {
                var banner = document.getElementById('fullFunnelBannerTarget') || close.closest('.full-funnel-background');
                if (banner) banner.style.display = 'none';
                return;
            }
            var activate = e.target.closest('[data-element-name="messaging-banner"] button');
            if (activate) window.location.href = @json(route('deals'));
        });
    })();
    </script>
    @include('components.layout.footer')
 


    

    
    
    




<div class="ReactModalPortal"></div><div class="ReactModalPortal"></div><link rel="prefetch" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/4754-c334e52610a7.js" as="script"><link rel="prefetch" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/7311-188a0c4d0179.js" as="script"><link rel="prefetch" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/8140-85725b62765c.js" as="script"><link rel="prefetch" href="https://cdn6.agoda.net/cdn-accom-web/js/assets/browser-bundle/ADSEFF-1308=A/property-4d8a141127db.js" as="script"><div id="drone-icon-container" style="display: none;"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><symbol id="9523eea2" viewBox="0 0 24 24"><g clip-path="url(#clip0_2_8378)"><path fill-rule="evenodd" clip-rule="evenodd" d="m3.666 2.643.082.071L12 10.966l8.253-8.252a.73.73 0 0 1 1.104.952l-.071.082L13.034 12l8.252 8.253a.731.731 0 0 1-.952 1.104l-.081-.071L12 13.034l-8.252 8.252a.73.73 0 0 1-1.105-.952l.071-.081L10.966 12 2.714 3.748a.73.73 0 0 1 .952-1.105Z"></path></g><defs><clipPath id="clip0_2_8378"><path d="M0 0h24v24H0z"></path></clipPath></defs></symbol></svg></div>
        





</body>
</html>